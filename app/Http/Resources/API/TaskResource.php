<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date?->format('d-m-Y H:i:s'),
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->name,
            ],
            'assignee_id' => $this->assignee_id,
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'created_by' => $this->created_by,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'dependencies' => TaskDependencyResource::collection($this->whenLoaded('dependencies')),
            'dependents' => DependentTaskResource::collection($this->whenLoaded('dependents')),
            'all_dependencies_completed' => $this->when($this->relationLoaded('dependencies'), fn() => $this->allDependenciesCompleted()),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
