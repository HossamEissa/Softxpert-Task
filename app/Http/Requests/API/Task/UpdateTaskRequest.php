<?php

namespace App\Http\Requests\API\Task;

use App\Rules\DependenciesDueDateRule;
use App\Rules\DependentsDueDateRule;
use App\Rules\NoCircularDependencyRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('task.update') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $taskId = $this->route('task')->id;

        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => ['sometimes', 'required', 'date', new DependenciesDueDateRule($this->dependency_ids), new DependentsDueDateRule($taskId),],
            'dependency_ids' => 'nullable|array',
            'dependency_ids.*' => ['required', 'integer', 'exists:tasks,id', Rule::notIn([$taskId]), new NoCircularDependencyRule($taskId),],
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
}
