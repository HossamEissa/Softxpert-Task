<?php

namespace App\Rules;

use App\Models\Task;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DependentsDueDateRule implements ValidationRule
{
    protected int $taskId;

    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $task = Task::find($this->taskId);

        if (!$task) {
            return;
        }

        $taskTitles = $task->dependents()->where('due_date', '<', $value)->pluck('title')->implode(', ');

        if ($taskTitles) {
            $fail("Cannot update {$attribute}. The following tasks depend on this task and have earlier due dates: {$taskTitles}");
        }

    }
}
