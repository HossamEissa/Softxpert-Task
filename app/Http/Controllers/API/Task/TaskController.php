<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Task\AssignTaskRequest;
use App\Http\Requests\API\Task\CreateTaskRequest;
use App\Http\Requests\API\Task\UpdateTaskRequest;
use App\Http\Requests\API\Task\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $user = $request->user();

        // Check if user has permission to view all tasks (manager)
        if ($user->hasPermissionTo('task.view-all')) {
            $tasks = $this->taskService->getAllTasks();
        } else {
            // User can only see their assigned tasks
            $tasks = $this->taskService->getUserTasks($user->id);
        }

        // Apply search, filter, sort from traits
        $tasks = $tasks->search($request->search ?? '')
            ->filter($request->all())
            ->sort($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->dynamicPaginate();

        return $this->respondWithCollection(TaskResource::collection($tasks));
    }

    // Done
    public function store(CreateTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated(), Auth::id());

            return $this->respondWithItem(new TaskResource($task), 'Task created successfully');
        } catch (\Throwable $e) {
            return $this->errorStatus($e->getMessage());
        }
    }


    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['dependencies', 'dependents', 'creator', 'assignee']);

        return $this->respondWithItem(new TaskResource($task));
    }


    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        try {
            $task = $this->taskService->updateTask($task, $request->validated());

            return $this->respondWithItem(new TaskResource($task), 'Task updated successfully');
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }


    public function assign(AssignTaskRequest $request, int $id): JsonResponse
    {
        $task = Task::with('dependencies')->find($id);

        if (!$task) {
            return $this->errorNotFound('Task not found');
        }

        $this->authorize('assign', $task);

        try {
            $result = $this->taskService->assignTask($task, $request->assignee_id);

            return $this->respond([
                'status' => $this->getSuccess(),
                'message' => 'Task assigned successfully. ' . count($result['assigned_tasks']) . ' task(s) assigned in total.',
                'data' => [
                    'task' => new TaskResource($result['main_task']),
                    'assigned_tasks' => $result['assigned_tasks'],
                ],
            ]);
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }

    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->errorNotFound('Task not found');
        }

        $this->authorize('updateStatus', $task);

        try {
            $task = $this->taskService->updateTaskStatus($task, $request->status);

            return $this->respondWithItem(new TaskResource($task), 'Task status updated successfully');
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return $this->errorNotFound('Task not found');
        }

        $this->authorize('delete', $task);

        try {
            $this->taskService->deleteTask($task);

            return $this->respondWithDeleted('Task deleted successfully');
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }
}
