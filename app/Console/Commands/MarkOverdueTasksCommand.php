<?php

namespace App\Console\Commands;

use App\Services\TaskService;
use Illuminate\Console\Command;

class MarkOverdueTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:mark-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark overdue tasks as delayed';

    protected TaskService $taskService;

    /**
     * Create a new command instance.
     */
    public function __construct(TaskService $taskService)
    {
        parent::__construct();
        $this->taskService = $taskService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for overdue tasks...');

        try {
            $result = $this->taskService->markOverdueTasks();

            if ($result['marked_count'] > 0) {
                $this->info("Marked {$result['marked_count']} task(s) as delayed:");
                foreach ($result['marked_tasks'] as $taskTitle) {
                    $this->line("  - {$taskTitle}");
                }
            } else {
                $this->info('No overdue tasks found.');
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error marking overdue tasks: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
