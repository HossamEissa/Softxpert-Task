<?php

namespace App\Http\Requests\API\Task;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class AssignTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('task.assign') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'assignee_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'assignee_id.required' => 'Assignee user ID is required',
            'assignee_id.exists' => 'The selected user does not exist',
        ];
    }

    /**
     * Additional validation after rules pass
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $task = $this->route('task');
            
            if ($task && $task->assignee_id !== null) {
                $validator->errors()->add(
                    'task',
                    'This task is already assigned.'
                );
            }
        });
    }
}
