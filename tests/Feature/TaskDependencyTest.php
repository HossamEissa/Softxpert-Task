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

test('can create task with dependencies', function () {
    $dependency1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dependency2 = Task::factory()->create(['due_date' => now()->addDays(2)]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'Main Task',
            'description' => 'Task with dependencies',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'dependency_ids' => [$dependency1->id, $dependency2->id],
        ]);

    $response->assertStatus(201);
    
    $task = Task::where('title', 'Main Task')->first();
    expect($task->dependencies)->toHaveCount(2);
    expect($task->dependencies->pluck('id'))->toContain($dependency1->id, $dependency2->id);
});

test('task due date must be after or equal to dependency due dates', function () {
    $dependency = Task::factory()->create(['due_date' => now()->addDays(10)]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'Main Task',
            'due_date' => now()->addDays(5)->format('Y-m-d'), // Before dependency
            'dependency_ids' => [$dependency->id],
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['due_date']);
});

test('assigning task also assigns all unassigned dependencies', function () {
    // Create dependency chain
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => null,
    ]);
    
    $dep2 = Task::factory()->create([
        'due_date' => now()->addDays(2),
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => null,
    ]);
    
    $mainTask = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => null,
    ]);
    
    $mainTask->dependencies()->attach([$dep1->id, $dep2->id]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson("/api/tasks/{$mainTask->id}/assign", [
            'assignee_id' => $this->user->id,
        ]);

    $response->assertStatus(200)
        ->assertJson(['status' => true]);

    // Verify all tasks are assigned to the same user
    expect($mainTask->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep1->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep2->fresh()->assignee_id)->toBe($this->user->id);
    
    // Verify all tasks have assigned status
    expect($mainTask->fresh()->status)->toBe(TaskStatusEnum::Assigned);
    expect($dep1->fresh()->status)->toBe(TaskStatusEnum::Assigned);
    expect($dep2->fresh()->status)->toBe(TaskStatusEnum::Assigned);
});

test('assigning task with nested dependencies assigns entire chain', function () {
    // Create nested dependency chain: mainTask -> dep1 -> dep2
    $dep2 = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Pending,
    ]);
    
    $dep1 = Task::factory()->create([
        'due_date' => now()->addDays(3),
        'status' => TaskStatusEnum::Pending,
    ]);
    $dep1->dependencies()->attach($dep2->id);
    
    $mainTask = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::Pending,
    ]);
    $mainTask->dependencies()->attach($dep1->id);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson("/api/tasks/{$mainTask->id}/assign", [
            'assignee_id' => $this->user->id,
        ]);

    $response->assertStatus(200);

    // Verify entire chain is assigned
    expect($mainTask->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep1->fresh()->assignee_id)->toBe($this->user->id);
    expect($dep2->fresh()->assignee_id)->toBe($this->user->id);
});

test('already assigned dependencies are not reassigned', function () {
    $anotherUser = User::factory()->create();
    $anotherUser->assignRole('user');
    
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

    $this->actingAs($this->manager, 'sanctum')
        ->postJson("/api/tasks/{$mainTask->id}/assign", [
            'assignee_id' => $this->user->id,
        ]);

    // Main task assigned to user
    expect($mainTask->fresh()->assignee_id)->toBe($this->user->id);
    
    // Dependency stays with original user
    expect($dep1->fresh()->assignee_id)->toBe($anotherUser->id);
});

test('updating task with new dependencies syncs correctly', function () {
    $dep1 = Task::factory()->create(['due_date' => now()->addDays(1)]);
    $dep2 = Task::factory()->create(['due_date' => now()->addDays(2)]);
    $dep3 = Task::factory()->create(['due_date' => now()->addDays(3)]);
    
    $task = Task::factory()->create(['due_date' => now()->addDays(10)]);
    $task->dependencies()->attach([$dep1->id, $dep2->id]);

    expect($task->dependencies)->toHaveCount(2);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => $task->title,
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'dependency_ids' => [$dep2->id, $dep3->id], // Remove dep1, add dep3
        ]);

    $response->assertStatus(200);
    
    $task->refresh();
    expect($task->dependencies)->toHaveCount(2);
    expect($task->dependencies->pluck('id'))->toContain($dep2->id, $dep3->id);
    expect($task->dependencies->pluck('id'))->not->toContain($dep1->id);
});
