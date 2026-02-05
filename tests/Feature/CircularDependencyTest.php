<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    
    $this->manager = User::where('email', 'manager@admin.com')->first();
});

test('task cannot depend on itself', function () {
    $task = Task::factory()->create(['due_date' => now()->addDays(5)]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => $task->title,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'dependency_ids' => [$task->id], // Self-reference
        ]);

    // Validation error can be on dependency_ids or dependency_ids.0
    $response->assertStatus(422);
});

test('detects circular dependency in two tasks', function () {
    // Create Task A and Task B
    $taskA = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $taskB = Task::factory()->create(['due_date' => now()->addDays(10)]);

    // Make B depend on A
    $taskB->dependencies()->attach($taskA->id);

    // Try to make A depend on B (circular)
    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$taskA->id}", [
            'title' => $taskA->title,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'dependency_ids' => [$taskB->id],
        ]);

    $response->assertStatus(422);
});

test('detects circular dependency in three tasks', function () {
    // Create chain: C -> B -> A
    $taskA = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $taskB = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $taskC = Task::factory()->create(['due_date' => now()->addDays(10)]);

    $taskB->dependencies()->attach($taskA->id);
    $taskC->dependencies()->attach($taskB->id);

    // Try to make A depend on C (creates circle: C -> B -> A -> C)
    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$taskA->id}", [
            'title' => $taskA->title,
            'due_date' => now()->addDays(12)->format('Y-m-d'),
            'dependency_ids' => [$taskC->id],
        ]);

    $response->assertStatus(422);
});

test('detects circular dependency in complex chain', function () {
    // Create chain: D -> C -> B -> A
    $taskA = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $taskB = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $taskC = Task::factory()->create(['due_date' => now()->addDays(5)]);
    $taskD = Task::factory()->create(['due_date' => now()->addDays(10)]);

    $taskB->dependencies()->attach($taskA->id);
    $taskC->dependencies()->attach($taskB->id);
    $taskD->dependencies()->attach($taskC->id);

    // Try to make B depend on D (creates circle)
    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$taskB->id}", [
            'title' => $taskB->title,
            'due_date' => now()->addDays(12)->format('Y-m-d'),
            'dependency_ids' => [$taskA->id, $taskD->id],
        ]);

    $response->assertStatus(422);
});
test('allows valid dependency chain without circular reference', function () {
    $taskA = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $taskB = Task::factory()->create(['due_date' => now()->addDays(3)]);
    $taskC = Task::factory()->create(['due_date' => now()->addDays(5)]);

    // Build chain: C -> B -> A (no circle)
    $taskB->dependencies()->attach($taskA->id);
    
    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$taskC->id}", [
            'title' => $taskC->title,
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'dependency_ids' => [$taskB->id],
        ]);

    $response->assertStatus(200);
    expect($taskC->fresh()->dependencies->pluck('id'))->toContain($taskB->id);
});

test('allows task to have multiple dependencies without circular reference', function () {
    $taskA = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $taskB = Task::factory()->create(['due_date' => now()->addDays(2)]);
    $taskC = Task::factory()->create(['due_date' => now()->addDays(3)]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'Task with multiple deps',
            'description' => 'Task description',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'dependency_ids' => [$taskA->id, $taskB->id, $taskC->id],
        ]);

    // Note: POST returns 201 Created for new resources
    $response->assertSuccessful();
    
    $task = Task::where('title', 'Task with multiple deps')->first();
    expect($task->dependencies)->toHaveCount(3);
});
