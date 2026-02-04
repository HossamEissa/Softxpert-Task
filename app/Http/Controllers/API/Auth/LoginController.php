<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\LoginRequest;
use App\Http\Resources\API\Profile\UserProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::query()->where('email' , $data['email'])->first();

        if (!$user) return $this->errorNotFound(__("These credentials do not match our records."));

        if (!$user->hasVerifiedEmail()) {
            return $this->errorForbidden(__("Your email address needs to be verified."));
        }

        if (!Hash::check($data['password'], $user->password)) {
            return $this->errorNotFound(__("These credentials do not match our records."));
        }


        $deviceName = $request->post('device_name', $request->userAgent());
        $token = $user->createToken($deviceName)->plainTextToken;

        $finalData = [
            'access_token' => $token,
            'user' => new UserProfileResource($user),
        ];

        $user->update(['last_login_at' => now()]);

        return $this->respondWithItem($finalData, __("Login successful."));

    }
}
