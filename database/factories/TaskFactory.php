<?php

namespace Database\Factories;

use App\Enum\TaskStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'status' => TaskStatusEnum::Pending,
            'created_by' => User::factory(),
            'assignee_id' => null,
        ];
    }

    /**
     * Indicate that the task is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatusEnum::Assigned,
            'assignee_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the task is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatusEnum::InProgress,
            'assignee_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatusEnum::Completed,
            'assignee_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the task is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatusEnum::Cancelled,
        ]);
    }

    /**
     * Indicate that the task is delayed.
     */
    public function delayed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatusEnum::Delayed,
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the task is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
