<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Enum\TaskStatusEnum;

class TaskPolicy
{
    /**
     * Determine whether the user can view any tasks.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['task.view', 'task.view-all']);
    }

    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasPermissionTo('task.view-all')) {
            return true;
        }

        return $user->hasPermissionTo('task.view') && $task->assignee_id === $user->id;
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('task.create');
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('task.update');
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('task.delete');
    }

   
    public function assign(User $user, Task $task): bool
    {
        return $user->hasPermissionTo('task.assign');
    }

   
    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->hasPermissionTo('task.update')) {
            return true;
        }

        return $user->hasPermissionTo('task.update-status') && $task->assignee_id === $user->id;
    }
}
