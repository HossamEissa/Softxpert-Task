<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Auth\RegistrationRequest;
use App\Mail\VerificationEmail;
use App\Models\Company;
use App\Models\Member;
use App\Models\User;
use App\Traits\ApiResponder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RegistrationController extends Controller
{
    use ApiResponder;


    public function register(RegistrationRequest $request)
    {
        $data = $request->validated();
        DB::beginTransaction();

        try {
            $data = $this->uploadFiles($data);

            $user = User::query()->create($data);

            $otp = $user->generateOTPCode();

            Mail::to($user)->send(new VerificationEmail($otp));

            DB::commit();

            return $this->respondWithMessage(__("You’re almost there! Please verify your email by checking the OTP we’ve sent to your inbox"));

        } catch (\Throwable $exception) {
            DB::rollBack();
            return $this->errorDatabase($exception->getMessage());
        }

    }


    public static function uploadFiles(mixed $data): mixed
    {
        if (isset($data['avatar'])) {
            $data['avatar'] = uploadFile($data['avatar'], 'users/avatars');
        }
        return $data;
    }

}
