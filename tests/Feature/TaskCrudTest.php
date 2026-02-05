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

test('manager can create a task', function () {
    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'id',
                'title',
                'description',
                'due_date',
                'status',
            ]
        ]);

    expect(Task::where('title', 'New Task')->exists())->toBeTrue();
});

test('user cannot create a task', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

    $response->assertStatus(403);
});

test('task creation requires title and due date', function () {
    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'description' => 'Task Description',
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'due_date']);
});

test('due date cannot be in the past', function () {
    $response = $this->actingAs($this->manager, 'sanctum')
        ->postJson('/api/tasks', [
            'title' => 'New Task',
            'due_date' => now()->subDays(1)->format('Y-m-d'),
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['due_date']);
});

test('manager can view all tasks', function () {
    Task::factory()->count(5)->create();

    $response = $this->actingAs($this->manager, 'sanctum')
        ->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'due_date']
                ],
                'current_page',
                'per_page',
                'total',
            ]
        ]);
});

test('user can only view assigned tasks', function () {
    // Create tasks - some assigned to user, some not
    Task::factory()->count(3)->create(['assignee_id' => $this->user->id]);
    Task::factory()->count(2)->create(['assignee_id' => null]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson('/api/tasks');

    $response->assertStatus(200);
    
    $tasks = $response->json('data.data');
    expect($tasks)->toHaveCount(3);
    
    foreach ($tasks as $task) {
        expect($task['assignee']['id'])->toBe($this->user->id);
    }
});

test('manager can view single task', function () {
    $task = Task::factory()->create(['title' => 'Test Task']);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'data' => [
                'id' => $task->id,
                'title' => 'Test Task',
            ]
        ]);
});

test('user cannot view unassigned task', function () {
    $task = Task::factory()->create(['assignee_id' => null]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('manager can update a task', function () {
    $task = Task::factory()->create(['title' => 'Original Title']);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'data' => [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
            ]
        ]);

    expect($task->fresh()->title)->toBe('Updated Title');
});

test('user cannot update a task', function () {
    $task = Task::factory()->create(['assignee_id' => $this->user->id]);

    $response = $this->actingAs($this->user, 'sanctum')
        ->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

    $response->assertStatus(403);
});

test('can search tasks by title', function () {
    Task::factory()->create(['title' => 'Important Task']);
    Task::factory()->create(['title' => 'Regular Task']);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->getJson('/api/tasks?search=Important');

    $response->assertStatus(200);
    $tasks = $response->json('data.data');
    
    expect($tasks)->toHaveCount(1);
    expect($tasks[0]['title'])->toBe('Important Task');
});

test('can filter tasks by status', function () {
    Task::factory()->count(2)->create(['status' => TaskStatusEnum::Pending]);
    Task::factory()->count(3)->create(['status' => TaskStatusEnum::Completed]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->getJson('/api/tasks?status=completed');

    $response->assertStatus(200);
    $tasks = $response->json('data.data');
    
    expect($tasks)->toHaveCount(3);
});

test('can sort tasks by due date', function () {
    Task::factory()->create(['due_date' => now()->addDays(5)]);
    Task::factory()->create(['due_date' => now()->addDays(1)]);
    Task::factory()->create(['due_date' => now()->addDays(3)]);

    $response = $this->actingAs($this->manager, 'sanctum')
        ->getJson('/api/tasks?sort_by=due_date&sort_order=asc');

    $response->assertStatus(200);
    $tasks = $response->json('data.data');
    
    expect($tasks[0]['due_date'])->toBeLessThan($tasks[1]['due_date']);
    expect($tasks[1]['due_date'])->toBeLessThan($tasks[2]['due_date']);
});
