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

test('manager can update task status to any value', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertStatus(200);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Completed);
});

test('user can update status of assigned task', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Assigned,
        'assignee_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in-progress',
        ]);

    $response->assertStatus(200);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::InProgress);
});

test('user cannot update status of unassigned task', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => null,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'in-progress',
        ]);

    $response->assertStatus(403);
});

test('user cannot update status of task assigned to someone else', function () {
    $anotherUser = User::factory()->create();
    $anotherUser->assignRole('user');
    
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Assigned,
        'assignee_id' => $anotherUser->id,
    ]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertStatus(403);
});

test('cannot complete task when dependencies are incomplete', function () {
    $dependency = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::InProgress,
        'assignee_id' => $this->user->id,
    ]);
    
    $task = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::InProgress,
        'assignee_id' => $this->user->id,
    ]);
    $task->dependencies()->attach($dependency->id);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('can complete task when all dependencies are completed', function () {
    $dependency = Task::factory()->create([
        'due_date' => now()->addDays(1),
        'status' => TaskStatusEnum::Completed,
        'assignee_id' => $this->user->id,
    ]);
    
    $task = Task::factory()->create([
        'due_date' => now()->addDays(5),
        'status' => TaskStatusEnum::InProgress,
        'assignee_id' => $this->user->id,
    ]);
    $task->dependencies()->attach($dependency->id);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertStatus(200);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Completed);
});

test('can complete task with no dependencies', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::InProgress,
        'assignee_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

    $response->assertStatus(200);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Completed);
});

test('status validation requires valid enum value', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => $this->user->id,
    ]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'invalid-status',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['status']);
});

test('can update task status through multiple transitions', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::Pending,
        'assignee_id' => $this->user->id,
    ]);

    // Pending -> Assigned
    $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'assigned']);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Assigned);

    // Assigned -> In Progress
    $this->actingAs($this->user, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'in-progress']);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::InProgress);

    // In Progress -> Completed
    $this->actingAs($this->user, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", ['status' => 'completed']);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Completed);
});

test('manager can cancel any task', function () {
    $task = Task::factory()->create([
        'status' => TaskStatusEnum::InProgress,
    ]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'cancelled',
        ]);

    $response->assertStatus(200);
    expect($task->fresh()->status)->toBe(TaskStatusEnum::Cancelled);
});
