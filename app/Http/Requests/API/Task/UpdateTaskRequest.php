<?php

namespace App\Http\Requests\API\Task;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('sanctum')->user()->can('task.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $taskId = $this->route('task');

        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'sometimes|required|date',
            'dependency_ids' => 'nullable|array',
            'dependency_ids.*' => [
                'required',
                'integer',
                'exists:tasks,id',
                Rule::notIn([$taskId]), // Cannot depend on itself
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'due_date.required' => 'Due date is required',
            'dependency_ids.*.exists' => 'One or more dependency tasks do not exist',
            'dependency_ids.*.not_in' => 'A task cannot depend on itself',
        ];
    }

    /**
     * Additional validation after rules pass
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $taskId = $this->route('task');
            $task = Task::find($taskId);

            if (!$task) {
                return;
            }

            // Validate circular dependencies if updating dependencies
            if ($this->has('dependency_ids') && is_array($this->dependency_ids)) {
                foreach ($this->dependency_ids as $dependencyId) {
                    if ($task->wouldCreateCircularDependency($dependencyId)) {
                        $dependencyTask = Task::find($dependencyId);
                        $validator->errors()->add(
                            'dependency_ids',
                            "Cannot add dependency '{$dependencyTask->title}' as it would create a circular dependency"
                        );
                        break;
                    }
                }
            }

            // If updating due_date, validate hierarchy
            if ($this->has('due_date')) {
                $newDueDate = $this->due_date;

                // Check if any dependents would be violated
                $invalidDependents = $task->dependents()
                    ->where('due_date', '<', $newDueDate)
                    ->get(['id', 'title', 'due_date']);

                if ($invalidDependents->isNotEmpty()) {
                    $taskTitles = $invalidDependents->pluck('title')->implode(', ');
                    $validator->errors()->add(
                        'due_date',
                        "Cannot update due date. The following tasks depend on this task and have earlier due dates: {$taskTitles}"
                    );
                }

                // If updating dependencies, check that they have due_date <= new due_date
                if ($this->has('dependency_ids') && is_array($this->dependency_ids)) {
                    $invalidDependencies = Task::whereIn('id', $this->dependency_ids)
                        ->where('due_date', '>', $newDueDate)
                        ->get(['id', 'title', 'due_date']);

                    if ($invalidDependencies->isNotEmpty()) {
                        $taskTitles = $invalidDependencies->pluck('title')->implode(', ');
                        $validator->errors()->add(
                            'due_date',
                            "The due date must be equal to or after the due dates of all dependency tasks. Invalid dependencies: {$taskTitles}"
                        );
                    }
                }
            } else if ($this->has('dependency_ids') && is_array($this->dependency_ids)) {
                // Only updating dependencies, validate with current due_date
                $invalidDependencies = Task::whereIn('id', $this->dependency_ids)
                    ->where('due_date', '>', $task->due_date)
                    ->get(['id', 'title', 'due_date']);

                if ($invalidDependencies->isNotEmpty()) {
                    $taskTitles = $invalidDependencies->pluck('title')->implode(', ');
                    $validator->errors()->add(
                        'dependency_ids',
                        "Invalid dependencies. The following tasks have due dates after this task's due date: {$taskTitles}"
                    );
                }
            }

        });
    }
}
