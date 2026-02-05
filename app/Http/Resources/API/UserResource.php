<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'country_code' => $this->country_code,
            'country_calling_code' => $this->country_calling_code,
            'phone_number' => $this->phone_number,
            'role' => $this->whenLoaded('roles', function () {
                return $this->roles->first()?->name;
            }),
            'last_login_at' => $this->last_login_at?->format('Y-m-d H:i:s'),
        ];
    }
}
