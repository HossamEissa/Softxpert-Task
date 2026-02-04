<?php

namespace App\Http\Requests\API\Task;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    TaskStatusEnum::InProgress->value,
                    TaskStatusEnum::Completed->value,
                    TaskStatusEnum::Cancelled->value,
                    TaskStatusEnum::Delayed->value,
                ]),
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status value',
        ];
    }

    /**
     * Additional validation after rules pass
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $taskId = $this->route('task');
            $task = Task::with('dependencies')->find($taskId);
            $newStatus = $this->status;
            $user = $this->user();

            if (!$task) {
                return;
            }

            // Check if user is trying to change status from completed/delayed/cancelled
            $restrictedStatuses = [
                TaskStatusEnum::Completed->value,
                TaskStatusEnum::Delayed->value,
                TaskStatusEnum::Cancelled->value,
            ];

            if (in_array($task->status->value, $restrictedStatuses)) {
                // Check if user has manager role (can change these statuses)
                $hasManagerPermission = $user->hasPermissionTo('task.assign') || $user->hasRole('manager');
                
                if (!$hasManagerPermission) {
                    $validator->errors()->add(
                        'status',
                        'Only managers can change the status of completed, delayed, or cancelled tasks'
                    );
                    return;
                }
            }

            // If trying to mark as completed, validate all dependencies are completed
            if ($newStatus === TaskStatusEnum::Completed->value) {
                if (!$task->allDependenciesCompleted()) {
                    $incompleteDependencies = $task->dependencies()
                        ->where('status', '!=', TaskStatusEnum::Completed->value)
                        ->get(['id', 'title', 'status']);
                    
                    $taskTitles = $incompleteDependencies->pluck('title')->implode(', ');
                    $validator->errors()->add(
                        'status',
                        "Cannot mark task as completed. The following dependency tasks are not completed: {$taskTitles}"
                    );
                }
            }
        });
    }
}
