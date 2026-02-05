<?php

namespace App\Http\Resources\API\Auth;

use App\Http\Resources\API\Profile\CompanyProfileResource;
use App\Http\Resources\API\Profile\MemberProfileResource;
use App\Models\Company;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "avatar" => $this->avatar,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            'role_name' => $this->roles()->first()?->name,
            "last_login_at" => $this->last_login_at,

        ];
    }


}
