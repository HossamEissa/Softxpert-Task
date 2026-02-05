<?php

namespace App\Services;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use App\Models\TaskDependency;
use Illuminate\Support\Facades\DB;
use Exception;

class TaskService
{
    public function createTask(array $data, int $createdBy): Task
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $task = Task::create($data);

            if (!empty($data['dependency_ids'])) {
                $this->addDependenciesToTask($task, $data['dependency_ids']);
            }

            return $task->fresh(['dependencies', 'creator']);
        });
    }


    public function updateTask(Task $task, array $data): Task
    {
        return DB::transaction(function () use ($task, $data) {
            $task->fill($data);
            $task->save();

            if (array_key_exists('dependency_ids', $data)) {
                $task->dependencies()->sync($data['dependency_ids'] ?? []);
            }

            return $task->fresh(['dependencies', 'creator', 'assignee']);
        });
    }

    public function assignTask(Task $task, int $assigneeId): array
    {
        return DB::transaction(function () use ($task, $assigneeId) {
            $assignedTasks = [];

            $allDependencies = $task->getAllDependenciesRecursively();

            foreach ($allDependencies as $dependency) {
                if ($dependency->assignee_id === null || $dependency->status === TaskStatusEnum::Pending) {
                    $dependency->assignee_id = $assigneeId;
                    $dependency->status = TaskStatusEnum::Assigned;
                    $dependency->save();

                    $assignedTasks[] = [
                        'id' => $dependency->id,
                        'title' => $dependency->title,
                        'due_date' => $dependency->due_date,
                    ];
                }
            }

            $task->assignee_id = $assigneeId;
            $task->status = TaskStatusEnum::Assigned;
            $task->save();

            array_unshift($assignedTasks, [
                'id' => $task->id,
                'title' => $task->title,
                'due_date' => $task->due_date,
            ]);

            return [
                'main_task' => $task->fresh(['dependencies', 'creator', 'assignee']),
                'assigned_tasks' => $assignedTasks,
            ];
        });
    }


    public function updateTaskStatus(Task $task, string $status): Task
    {
        $task->status = TaskStatusEnum::from($status);
        $task->save();

        return $task->fresh(['dependencies', 'creator', 'assignee']);
    }


    public function deleteTask(Task $task): bool
    {
        return DB::transaction(function () use ($task) {

            $dependentTasks = $task->dependents;

            if ($dependentTasks->isNotEmpty()) {
                $taskTitles = $dependentTasks->pluck('title')->implode(', ');
                throw new Exception(
                    "Cannot delete this task. The following tasks depend on it: {$taskTitles}"
                );
            }

            $task->taskDependencies()->delete();

            return $task->delete();
        });
    }


    private function addDependenciesToTask(Task $task, array $dependencyIds): void
    {
        foreach ($dependencyIds as $dependencyId) {
            TaskDependency::create([
                'task_id' => $task->id,
                'dependency_id' => $dependencyId,
            ]);
        }
    }


    public function markOverdueTasks(): array
    {
        $now = now();

        $overdueTasks = Task::where('due_date', '<', $now)
            ->whereNotIn('status', [
                TaskStatusEnum::Completed,
                TaskStatusEnum::Cancelled,
                TaskStatusEnum::Delayed,
            ])->get();

        $markedTasks = [];
        $markedCount = 0;

        foreach ($overdueTasks as $task) {
            $markedTasks[] = $task->title;
            $task->status = TaskStatusEnum::Delayed;
            $task->save();
            $markedCount++;
        }

        return [
            'marked_count' => $markedCount,
            'marked_tasks' => $markedTasks,
        ];
    }

    public function getUserTasks(int $userId)
    {
        return Task::where('assignee_id', $userId)->with(['dependencies', 'creator', 'assignee']);
    }

    public function getAllTasks()
    {
        return Task::with(['dependencies', 'creator', 'assignee']);
    }
}
