<?php

use App\Models\Task;
use App\Enum\TaskStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('marks overdue tasks as delayed', function () {
    // Create overdue tasks
    $overdueTask1 = Task::factory()->create([
        'due_date' => now()->subDays(2),
        'status' => TaskStatusEnum::Pending,
    ]);
    
    $overdueTask2 = Task::factory()->create([
        'due_date' => now()->subDays(1),
        'status' => TaskStatusEnum::InProgress,
    ]);
    
    // Create non-overdue task
    $futureTask = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::Assigned,
    ]);

    Artisan::call('tasks:mark-overdue');

    expect($overdueTask1->fresh()->status)->toBe(TaskStatusEnum::Delayed);
    expect($overdueTask2->fresh()->status)->toBe(TaskStatusEnum::Delayed);
    expect($futureTask->fresh()->status)->toBe(TaskStatusEnum::Assigned);
});

test('does not mark completed tasks as delayed', function () {
    $completedOverdueTask = Task::factory()->create([
        'due_date' => now()->subDays(5),
        'status' => TaskStatusEnum::Completed,
    ]);

    Artisan::call('tasks:mark-overdue');

    expect($completedOverdueTask->fresh()->status)->toBe(TaskStatusEnum::Completed);
});

test('does not mark cancelled tasks as delayed', function () {
    $cancelledOverdueTask = Task::factory()->create([
        'due_date' => now()->subDays(3),
        'status' => TaskStatusEnum::Cancelled,
    ]);

    Artisan::call('tasks:mark-overdue');

    expect($cancelledOverdueTask->fresh()->status)->toBe(TaskStatusEnum::Cancelled);
});

test('does not mark already delayed tasks again', function () {
    $delayedTask = Task::factory()->create([
        'due_date' => now()->subDays(10),
        'status' => TaskStatusEnum::Delayed,
    ]);

    Artisan::call('tasks:mark-overdue');

    expect($delayedTask->fresh()->status)->toBe(TaskStatusEnum::Delayed);
});

test('marks multiple overdue tasks in bulk', function () {
    $overdueTasks = Task::factory()->count(10)->create([
        'due_date' => now()->subDays(1),
        'status' => TaskStatusEnum::Assigned,
    ]);

    Artisan::call('tasks:mark-overdue');

    foreach ($overdueTasks as $task) {
        expect($task->fresh()->status)->toBe(TaskStatusEnum::Delayed);
    }
});

test('command returns correct count of marked tasks', function () {
    Task::factory()->count(5)->create([
        'due_date' => now()->subDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);

    Task::factory()->count(3)->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);

    Artisan::call('tasks:mark-overdue');
    $output = Artisan::output();

    expect($output)->toContain('5');
});
