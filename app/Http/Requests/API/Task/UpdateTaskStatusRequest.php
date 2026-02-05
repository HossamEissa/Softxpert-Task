<?php

namespace App\Http\Requests\API\Task;

use App\Enum\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');
        return $this->user()->can('updateStatus', $task);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(TaskStatusEnum::class)],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required',
            'status.enum' => 'Invalid status value',
        ];
    }

    /**
     * Additional validation after rules pass
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $task = $this->route('task');
            $newStatus = $this->status;
            $user = $this->user();

            if (!$task || !$newStatus) return;

            $isManager = $user->hasPermissionTo('task.update');

            if (!$isManager) {
                $allowedUserStatuses = [
                    TaskStatusEnum::InProgress->value,
                    TaskStatusEnum::Completed->value,
                ];

                if (!in_array($newStatus, $allowedUserStatuses)) {
                    $validator->errors()->add(
                        'status',
                        'Users can only update status to In Progress or Completed.'
                    );
                    return;
                }
            }

            $restrictedStatuses = [
                TaskStatusEnum::Completed->value,
                TaskStatusEnum::Delayed->value,
                TaskStatusEnum::Cancelled->value,
            ];

            if (in_array($task->status->value, $restrictedStatuses)) {
                if (!$isManager) {
                    $validator->errors()->add(
                        'status',
                        "Cannot update status. This task is currently {$task->status->name}. Only managers can change the status of completed, delayed, or cancelled tasks."
                    );
                    return;
                }
            }
        
            if ($newStatus === TaskStatusEnum::Completed->value) {
                $incompleteDependencies = $task->dependencies
                    ->where('status', '!=', TaskStatusEnum::Completed->value)
                    ->pluck('title')
                    ->implode(', ');

                if (!empty($incompleteDependencies)) {
                    $validator->errors()->add(
                        'status',
                        "Cannot mark task as completed. The following dependency tasks are not completed: {$incompleteDependencies}"
                    );
                }
            }
        });
    }
}
