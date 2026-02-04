<?php

namespace App\Rules;

use App\Models\Task;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoCircularDependencyRule implements ValidationRule
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

        if ($task->wouldCreateCircularDependency($value)) {
            $dependencyTitle = Task::whereId($value)->value('title');
            $fail("The {$attribute} '{$dependencyTitle}' would create a circular dependency");
        }

    }
}
