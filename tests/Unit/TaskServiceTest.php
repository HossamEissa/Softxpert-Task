<?php

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use App\Enum\TaskStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->taskService = new TaskService();
    $this->manager = User::where('email', 'manager@admin.com')->first();
    $this->user = User::where('email', 'user@admin.com')->first();
});

test('createTask creates task with correct data', function () {
    $data = [
        'title' => 'New Service Task',
        'description' => 'Test description',
        'due_date' => now()->addDays(5),
    ];

    $task = $this->taskService->createTask($data, $this->manager->id);

    expect($task)->toBeInstanceOf(Task::class);
    expect($task->title)->toBe('New Service Task');
    expect($task->created_by)->toBe($this->manager->id);
    expect($task->status)->toBe(TaskStatusEnum::Pending);
});

test('createTask adds dependencies correctly', function () {
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(2)]);

    $data = [
        'title' => 'Task with deps',
        'description' => 'Task description',
        'due_date' => now()->addDays(5),
        'dependency_ids' => [$dep1->id, $dep2->id],
    ];

    $task = $this->taskService->createTask($data, $this->manager->id);

    expect($task->dependencies)->toHaveCount(2);
});

test('updateTask updates task fields correctly', function () {
    $task = Task::factory()->create([
        'title' => 'Original',
        'description' => 'Original description',
    ]);

    $updatedTask = $this->taskService->updateTask($task, [
        'title' => 'Updated',
        'description' => 'Updated description',
        'due_date' => now()->addDays(10),
    ]);

    expect($updatedTask->title)->toBe('Updated');
    expect($updatedTask->description)->toBe('Updated description');
});

test('updateTask syncs dependencies', function () {
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(2)]);
    $dep3 = Task::factory()->create(['due_date' => now()->addDays(3)]);

    $task = Task::factory()->create(['due_date' => now()->addDays(10)]);
    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    $updatedTask = $this->taskService->updateTask($task, [
        'title' => $task->title,
        'due_date' => now()->addDays(10),
        'dependency_ids' => [$dep2->id, $dep3->id],
    ]);

    expect($updatedTask->dependencies)->toHaveCount(2);
    expect($updatedTask->dependencies->pluck('id'))->toContain($dep2->id, $dep3->id);
    expect($updatedTask->dependencies->pluck('id'))->not->toContain($dep1->id);
});

test('assignTask assigns main task and all unassigned dependencies', function () {
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);
    
    $dep2 = Task::factory()->create([
        'due_date' => now()->addDays(2),
        'status' => TaskStatusEnum::Pending,
    ]);
    
    $mainTask = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::Pending,
    ]);
    $mainTask->dependencies()->attach([$dep1->id, $dep2->id]);

    $result = $this->taskService->assignTask($mainTask, $this->user->id);

    expect($result)->toHaveKeys(['main_task', 'assigned_tasks']);
    expect($result['assigned_tasks'])->toHaveCount(3);
    expect($mainTask->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep1->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep2->fresh()->assignee_id)->toBe($this->user->id);
});

test('assignTask sets status to assigned', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Pending,
    ]);

    $this->taskService->assignTask($task, $this->user->id);

    expect($task->fresh()->status)->toBe(TaskStatusEnum::Assigned);
});

test('assignTask does not reassign already assigned dependencies', function () {
    $anotherUser = User::factory()->create();
    
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Completed,
        'assignee_id' => $anotherUser->id,
    ]);
    
    $mainTask = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::Pending,
    ]);
    $mainTask->dependencies()->attach($dep1->id);

    $this->taskService->assignTask($mainTask, $this->user->id);

    expect($mainTask->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep1->fresh()->assignee_id)->toBe($anotherUser->id);
});

test('updateTaskStatus updates status correctly', function () {
    $task = Task::factory()->create(['status' => TaskStatusEnum::Pending]);

    $updatedTask = $this->taskService->updateTaskStatus($task, 'in-progress');

    expect($updatedTask->status)->toBe(TaskStatusEnum::InProgress);
});

test('markOverdueTasks marks correct tasks', function () {
    Task::factory()->count(3)->create([
        'due_date' => now()->subDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);
    
    Task::factory()->count(2)->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);

    $result = $this->taskService->markOverdueTasks();

    expect($result['marked_count'])->toBe(3);
    expect($result['marked_tasks'])->toHaveCount(3);
});

test('getUserTasks returns only user assigned tasks', function () {
    Task::factory()->count(3)->create(['assignee_id' => $this->user->id]);
    Task::factory()->count(2)->create(['assignee_id' => $this->manager->id]);

    $tasks = $this->taskService->getUserTasks($this->user->id)->get();

    expect($tasks)->toHaveCount(3);
    foreach ($tasks as $task) {
        expect($task->assignee_id)->toBe($this->user->id);
    }
});

test('getAllTasks returns all tasks', function () {
    Task::factory()->count(5)->create();

    $tasks = $this->taskService->getAllTasks()->get();

    expect($tasks)->toHaveCount(5);
});
