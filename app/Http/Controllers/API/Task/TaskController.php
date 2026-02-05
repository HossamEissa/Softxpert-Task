<?php

namespace App\Http\Controllers\API\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Task\AssignTaskRequest;
use App\Http\Requests\API\Task\CreateTaskRequest;
use App\Http\Requests\API\Task\UpdateTaskRequest;
use App\Http\Requests\API\Task\UpdateTaskStatusRequest;
use App\Http\Resources\API\TaskCollection;
use App\Http\Resources\API\TaskResource;
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

        $query = $user->hasPermissionTo('task.view-all') ? Task::query() : Task::where('assignee_id', $user->id);

        $query->with(['dependencies', 'creator', 'assignee']);

        $tasks = $query->search()
            ->sort()
            ->filter()
            ->dynamicPaginate();

        return $this->respondWithRetrieved(new TaskCollection($tasks));
    }

    // Done
    public function store(CreateTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated(), Auth::id());

            return $this->respondWithCreated(new TaskResource($task), 'Task created successfully');
        } catch (\Throwable $e) {
            return $this->errorStatus($e->getMessage());
        }
    }

    // Done
    public function show(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['dependencies', 'dependents', 'creator', 'assignee']);

        return $this->respondWithRetrieved(new TaskResource($task));
    }

    // Done
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        try {
            $task = $this->taskService->updateTask($task, $request->validated());

            return $this->respondWithUpdated(new TaskResource($task), 'Task updated successfully');
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }

    // Done
    public function assign(AssignTaskRequest $request, Task $task): JsonResponse
    {
        $task->load('dependencies');

        try {
            $result = $this->taskService->assignTask($task, $request->assignee_id);
            $data = [
                'task' => new TaskResource($result['main_task']),
                'assigned_tasks' => $result['assigned_tasks'],
            ];
            $message =  'Task assigned successfully. ' . count($result['assigned_tasks']) . ' task(s) assigned in total.';
            return $this->respondWithItem($data, $message);
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }
    // Done
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        try {
            $task = $this->taskService->updateTaskStatus($task, $request->status);

            return $this->respondWithItem(new TaskResource($task), 'Task status updated successfully');
        } catch (Exception $e) {
            return $this->setStatusCode(400)->errorStatus($e->getMessage());
        }
    }
}
