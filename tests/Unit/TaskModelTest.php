<?php

use App\Models\Task;
use App\Models\User;
use App\Enum\TaskStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('task has creator relationship', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['created_by' => $user->id]);

    expect($task->creator)->toBeInstanceOf(User::class);
    expect($task->creator->id)->toBe($user->id);
});

test('task has assignee relationship', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['assignee_id' => $user->id]);

    expect($task->assignee)->toBeInstanceOf(User::class);
    expect($task->assignee->id)->toBe($user->id);
});

test('task can have multiple dependencies', function () {
    $task = Task::factory()->create(['due_date' => now()->addDays(10)]);
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(2)]);

    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    expect($task->dependencies)->toHaveCount(2);
    expect($task->dependencies->pluck('id'))->toContain($dep1->id, $dep2->id);
});

test('task can have multiple dependents', function () {
    $dependency = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $task1 = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $task2 = Task::factory()->create(['due_date' => now()->addDays(10)]);

    $task1->dependencies()->attach($dependency->id);
    $task2->dependencies()->attach($dependency->id);

    expect($dependency->dependents)->toHaveCount(2);
    expect($dependency->dependents->pluck('id'))->toContain($task1->id, $task2->id);
});

test('allDependenciesCompleted returns true when no dependencies', function () {
    $task = Task::factory()->create();

    expect($task->allDependenciesCompleted())->toBeTrue();
});

test('allDependenciesCompleted returns true when all dependencies completed', function () {
    $task = Task::factory()->create(['due_date' => now()->addDays(10)]);
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Completed,
    ]);
    $dep2 = Task::factory()->create([
        'due_date' => now()->addDays(2),
        'status' => TaskStatusEnum::Completed,
    ]);

    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    expect($task->allDependenciesCompleted())->toBeTrue();
});

test('allDependenciesCompleted returns false when some dependencies incomplete', function () {
    $task = Task::factory()->create(['due_date' => now()->addDays(10)]);
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Completed,
    ]);
    $dep2 = Task::factory()->create([
        'due_date' => now()->addDays(2),
        'status' => TaskStatusEnum::InProgress,
    ]);

    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    expect($task->allDependenciesCompleted())->toBeFalse();
});

test('getAllDependenciesRecursively returns all nested dependencies', function () {
    // Create chain: task -> dep1 -> dep2
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $dep1->dependencies()->attach($dep2->id);
    
    $task = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $task->dependencies()->attach($dep1->id);

    $allDeps = $task->getAllDependenciesRecursively();

    expect($allDeps)->toHaveCount(2);
    expect($allDeps->pluck('id'))->toContain($dep1->id, $dep2->id);
});

test('getAllDependenciesRecursively handles multiple branches', function () {
    // Create diamond structure: task -> dep1, dep2 -> dep3
    $dep3 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $dep1->dependencies()->attach($dep3->id);
    
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $dep2->dependencies()->attach($dep3->id);
    
    $task = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    $allDeps = $task->getAllDependenciesRecursively();

    // Should have 3 unique dependencies (dep1, dep2, dep3)
    expect($allDeps)->toHaveCount(3);
    expect($allDeps->pluck('id')->unique())->toHaveCount(3);
});

test('wouldCreateCircularDependency detects self-reference', function () {
    $task = Task::factory()->create();

    expect($task->wouldCreateCircularDependency($task->id))->toBeTrue();
});

test('wouldCreateCircularDependency detects two-task circle', function () {
    $task1 = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $task2 = Task::factory()->create(['due_date' => now()->addDays(10)]);

    $task2->dependencies()->attach($task1->id);

    expect($task1->wouldCreateCircularDependency($task2->id))->toBeTrue();
});

test('wouldCreateCircularDependency detects multi-task circle', function () {
    $task1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $task2 = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $task3 = Task::factory()->create(['due_date' => now()->addDays(5)]);

    $task2->dependencies()->attach($task1->id);
    $task3->dependencies()->attach($task2->id);

    expect($task1->wouldCreateCircularDependency($task3->id))->toBeTrue();
});

test('wouldCreateCircularDependency allows valid dependencies', function () {
    $task1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $task2 = Task::factory()->create(['due_date' => now()->addDays(5)]);

    expect($task2->wouldCreateCircularDependency($task1->id))->toBeFalse();
});

test('due_date is cast to datetime', function () {
    $task = Task::factory()->create(['due_date' => '2026-03-01']);

    expect($task->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('status is cast to enum', function () {
    $task = Task::factory()->create(['status' => TaskStatusEnum::Pending]);

    expect($task->status)->toBeInstanceOf(TaskStatusEnum::class);
    expect($task->status)->toBe(TaskStatusEnum::Pending);
});
