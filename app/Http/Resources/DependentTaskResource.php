<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DependentTaskResource extends JsonResource
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
            'status' => [
                'value' => $this->status?->value,
                'label' => $this->status?->name,
            ],
            'due_date' => $this->due_date?->format('Y-m-d H:i:s'),
        ];
    }
}
