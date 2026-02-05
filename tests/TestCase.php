<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create an authenticated user with manager role.
     */
    protected function actAsManager()
    {
        $manager = \App\Models\User::where('email', 'manager@admin.com')->first();
        return $this->actingAs($manager, 'sanctum');
    }

    /**
     * Create an authenticated user with user role.
     */
    protected function actAsUser()
    {
        $user = \App\Models\User::where('email', 'user@admin.com')->first();
        return $this->actingAs($user, 'sanctum');
    }

    /**
     * Create a task with dependencies for testing.
     */
    protected function createTaskWithDependencies(int $dependencyCount = 2)
    {
        $dependencies = \App\Models\Task::factory()
            ->count($dependencyCount)
            ->create([
                'due_date' => now()->addDays(1),
            ]);

        $task = \App\Models\Task::factory()->create([
            'due_date' => now()->addDays(10),
        ]);

        $task->dependencies()->attach($dependencies->pluck('id'));

        return $task;
    }
}
