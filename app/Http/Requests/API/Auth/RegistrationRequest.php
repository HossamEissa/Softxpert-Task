<?php

namespace App\Http\Requests\API\Auth;

use App\Http\Requests\API\Auth\ProfileTypes\DriverRequest;
use App\Http\Requests\API\Auth\ProfileTypes\MemberRequest;
use App\Http\Requests\API\Auth\ProfileTypes\ProfileTypeRequest;
use App\Http\Requests\API\Auth\ProfileTypes\CompanyRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    protected ProfileTypeRequest $profileTypeRequest;

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
        return array_merge($this->profileTypeRequest->rules(), [
            'avatar' => ['nullable', 'image'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'status' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'min:8', Password::min(8)->letters()->symbols()->numbers()->mixedCase()->uncompromised()],
        ]);
    }

}
