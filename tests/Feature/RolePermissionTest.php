<?php

use App\Models\Task;
use App\Models\User;
use App\Enum\TaskStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    
    $this->manager = User::where('email', 'manager@admin.com')->first();
    $this->user = User::where('email', 'user@admin.com')->first();
});

test('manager has task.create permission', function () {
    expect($this->manager->hasPermissionTo('task.create'))->toBeTrue();
});

test('user does not have task.create permission', function () {
    expect($this->user->hasPermissionTo('task.create'))->toBeFalse();
});

test('manager has task.update permission', function () {
    expect($this->manager->hasPermissionTo('task.update'))->toBeTrue();
});

test('user does not have task.update permission', function () {
    expect($this->user->hasPermissionTo('task.update'))->toBeFalse();
});

test('manager has task.view-all permission', function () {
    expect($this->manager->hasPermissionTo('task.view-all'))->toBeTrue();
});

test('user has task.view permission for assigned tasks only', function () {
    expect($this->user->hasPermissionTo('task.view'))->toBeTrue();
    expect($this->user->hasPermissionTo('task.view-all'))->toBeFalse();
});

test('manager has task.assign permission', function () {
    expect($this->manager->hasPermissionTo('task.assign'))->toBeTrue();
});

test('user does not have task.assign permission', function () {
    expect($this->user->hasPermissionTo('task.assign'))->toBeFalse();
});

test('manager can create task through policy', function () {
    $task = new Task();
    expect($this->manager->can('create', $task))->toBeTrue();
});

test('user cannot create task through policy', function () {
    $task = new Task();
    expect($this->user->can('create', $task))->toBeFalse();
});

test('manager can view any task through policy', function () {
    $task = Task::factory()->create();
    expect($this->manager->can('view', $task))->toBeTrue();
});

test('user can view assigned task through policy', function () {
    $task = Task::factory()->create(['assignee_id' => $this->user->id]);
    expect($this->user->can('view', $task))->toBeTrue();
});

test('user cannot view unassigned task through policy', function () {
    $task = Task::factory()->create(['assignee_id' => null]);
    expect($this->user->can('view', $task))->toBeFalse();
});

test('user cannot view task assigned to someone else through policy', function () {
    $anotherUser = User::factory()->create();
    $task = Task::factory()->create(['assignee_id' => $anotherUser->id]);
    
    expect($this->user->can('view', $task))->toBeFalse();
});

test('manager can update any task through policy', function () {
    $task = Task::factory()->create();
    expect($this->manager->can('update', $task))->toBeTrue();
});

test('user cannot update task through policy', function () {
    $task = Task::factory()->create(['assignee_id' => $this->user->id]);
    expect($this->user->can('update', $task))->toBeFalse();
});

test('manager can assign task through policy', function () {
    $task = Task::factory()->create();
    expect($this->manager->can('assign', $task))->toBeTrue();
});

test('user cannot assign task through policy', function () {
    $task = Task::factory()->create();
    expect($this->user->can('assign', $task))->toBeFalse();
});

test('manager can update any task status through policy', function () {
    $task = Task::factory()->create(['assignee_id' => $this->user->id]);
    expect($this->manager->can('updateStatus', $task))->toBeTrue();
});

test('user can update status of assigned task through policy', function () {
    $task = Task::factory()->create(['assignee_id' => $this->user->id]);
    expect($this->user->can('updateStatus', $task))->toBeTrue();
});

test('user cannot update status of unassigned task through policy', function () {
    $task = Task::factory()->create(['assignee_id' => null]);
    expect($this->user->can('updateStatus', $task))->toBeFalse();
});

test('user cannot update status of task assigned to someone else through policy', function () {
    $anotherUser = User::factory()->create();
    $task = Task::factory()->create(['assignee_id' => $anotherUser->id]);
    
    expect($this->user->can('updateStatus', $task))->toBeFalse();
});

test('manager role has all required permissions', function () {
    $requiredPermissions = [
        'task.create',
        'task.view-all',
        'task.update',
        'task.assign',
    ];

    foreach ($requiredPermissions as $permission) {
        expect($this->manager->hasPermissionTo($permission))
            ->toBeTrue("Manager should have {$permission} permission");
    }
});

test('user role has limited permissions', function () {
    $allowedPermissions = [
        'task.view',
        'task.update-status',
    ];

    $deniedPermissions = [
        'task.create',
        'task.view-all',
        'task.update',
        'task.assign',
    ];

    foreach ($allowedPermissions as $permission) {
        expect($this->user->hasPermissionTo($permission))
            ->toBeTrue("User should have {$permission} permission");
    }

    foreach ($deniedPermissions as $permission) {
        expect($this->user->hasPermissionTo($permission))
            ->toBeFalse("User should not have {$permission} permission");
    }
});
