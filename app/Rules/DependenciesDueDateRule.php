<?php

namespace App\Rules;

use App\Models\Task;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DependenciesDueDateRule implements ValidationRule
{
    protected ?array $dependencyIds;

    public function __construct(?array $dependencyIds = null)
    {
        $this->dependencyIds = $dependencyIds;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->dependencyIds)) {
            return;
        }
        $taskTitles = Task::whereIn('id', $this->dependencyIds)->where('due_date', '>', $value)->pluck('title')->implode(', ');

        if ($taskTitles) {
            $fail("The due date must be equal to or after the due dates of all dependency tasks. Invalid dependencies: {$taskTitles}");
        }
    }
}
