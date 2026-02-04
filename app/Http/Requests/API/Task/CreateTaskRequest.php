<?php

namespace App\Http\Requests\API\Task;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'dependency_ids' => 'nullable|array',
            'dependency_ids.*' => 'required_with:dependency_ids|integer|exists:tasks,id',
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
            'due_date.after_or_equal' => 'Due date must be today or a future date',
            'dependency_ids.*.exists' => 'One or more dependency tasks do not exist',
        ];
    }

    /**
     * Additional validation after rules pass
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('dependency_ids') && is_array($this->dependency_ids)) {
                $dependencyIds = $this->dependency_ids;
                $dueDate = $this->due_date;

                $invalidDependencies = Task::whereIn('id', $dependencyIds)->where('due_date', '>', $dueDate)->get(['id', 'title', 'due_date']);

                if ($invalidDependencies->isNotEmpty()) {
                    $taskTitles = $invalidDependencies->pluck('title')->implode(', ');
                    $validator->errors()->add('due_date', "The due date must be equal to or after the due dates of all dependency tasks. Invalid dependencies: {$taskTitles}");
                }
            }
        });
    }
}
