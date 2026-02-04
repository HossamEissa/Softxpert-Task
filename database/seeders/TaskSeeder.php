<?php

namespace Database\Seeders;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users (assuming UserSeeder has created them)
        $manager = User::where('email', 'manager@admin.com')->first();
        $user = User::where('email', 'user@admin.com')->first();

        if (!$manager || !$user) {
            $this->command->warn('Users not found. Please run UserSeeder first.');
            return;
        }

        // Clear existing tasks and dependencies
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        TaskDependency::truncate();
        Task::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create tasks with dependency hierarchy
        // Scenario 1: Task A depends on B and C, B depends on D
        // Due dates: D (earliest) -> B -> C -> A (latest)
        
        $taskD = Task::create([
            'title' => 'Setup Database Schema',
            'description' => 'Design and implement the database schema for the project',
            'due_date' => now()->addDays(3),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        $taskC = Task::create([
            'title' => 'Setup Authentication System',
            'description' => 'Implement user authentication and authorization',
            'due_date' => now()->addDays(5),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        $taskB = Task::create([
            'title' => 'Create API Endpoints',
            'description' => 'Build RESTful API endpoints for the application',
            'due_date' => now()->addDays(7),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        $taskA = Task::create([
            'title' => 'Implement Frontend Integration',
            'description' => 'Connect frontend to backend API and test all features',
            'due_date' => now()->addDays(10),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        // Create dependencies
        // Task B depends on D
        TaskDependency::create([
            'task_id' => $taskB->id,
            'dependency_id' => $taskD->id,
        ]);

        // Task A depends on B and C
        TaskDependency::create([
            'task_id' => $taskA->id,
            'dependency_id' => $taskB->id,
        ]);

        TaskDependency::create([
            'task_id' => $taskA->id,
            'dependency_id' => $taskC->id,
        ]);

        // Scenario 2: Independent tasks
        $taskE = Task::create([
            'title' => 'Write Unit Tests',
            'description' => 'Write comprehensive unit tests for all modules',
            'due_date' => now()->addDays(8),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        // Scenario 3: Already assigned task
        $taskF = Task::create([
            'title' => 'Code Review',
            'description' => 'Review code changes and provide feedback',
            'due_date' => now()->addDays(2),
            'status' => TaskStatusEnum::Assigned,
            'assignee_id' => $user->id,
            'created_by' => $manager->id,
        ]);

        // Scenario 4: In-progress task
        $taskG = Task::create([
            'title' => 'Update Documentation',
            'description' => 'Update API documentation with latest changes',
            'due_date' => now()->addDays(4),
            'status' => TaskStatusEnum::InProgress,
            'assignee_id' => $user->id,
            'created_by' => $manager->id,
        ]);

        // Scenario 5: Overdue task (will be marked as delayed by scheduled command)
        $taskH = Task::create([
            'title' => 'Fix Critical Bug',
            'description' => 'Address the critical bug reported in production',
            'due_date' => now()->subDays(2), // Overdue
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        // Scenario 6: Completed task with dependencies
        $taskI = Task::create([
            'title' => 'Setup Project Repository',
            'description' => 'Initialize Git repository and setup CI/CD',
            'due_date' => now()->subDays(5),
            'status' => TaskStatusEnum::Completed,
            'assignee_id' => $user->id,
            'created_by' => $manager->id,
        ]);

        $taskJ = Task::create([
            'title' => 'Setup Development Environment',
            'description' => 'Configure local development environment',
            'due_date' => now()->subDays(3),
            'status' => TaskStatusEnum::Completed,
            'assignee_id' => $user->id,
            'created_by' => $manager->id,
        ]);

        // Task J depends on I (both completed)
        TaskDependency::create([
            'task_id' => $taskJ->id,
            'dependency_id' => $taskI->id,
        ]);

        // Scenario 7: Task with dependency on completed task
        $taskK = Task::create([
            'title' => 'Deploy to Staging',
            'description' => 'Deploy application to staging environment',
            'due_date' => now()->addDays(1),
            'status' => TaskStatusEnum::Pending,
            'created_by' => $manager->id,
        ]);

        // Task K depends on J (completed)
        TaskDependency::create([
            'task_id' => $taskK->id,
            'dependency_id' => $taskJ->id,
        ]);

        $this->command->info('Tasks seeded successfully!');
        $this->command->info('');
        $this->command->info('Sample Tasks Created:');
        $this->command->info('1. Task A (Frontend Integration) depends on B (API Endpoints) and C (Authentication)');
        $this->command->info('2. Task B (API Endpoints) depends on D (Database Schema)');
        $this->command->info('3. When assigning Task A, Tasks B, C, and D will be auto-assigned');
        $this->command->info('4. Task H is overdue and will be marked as delayed by scheduled command');
        $this->command->info('5. Tasks I and J are completed, demonstrating dependency completion validation');
    }
}
