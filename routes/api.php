<?php

use App\Http\Controllers\API\Auth\EmailVerificationController;
use App\Http\Controllers\API\Auth\ForgetPasswordController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegistrationController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Profile\ProfileController;
use App\Http\Controllers\API\Task\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


################################# Authentication ##############################################
Route::middleware(['guest'])->group(function () {
    Route::post('register', [RegistrationController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);
    Route::post('forgot-password', [ForgetPasswordController::class, 'forgotPassword']);
    Route::post('resend-code', [EmailVerificationController::class, 'resendEmailVerification']);
    Route::post('verify', [EmailVerificationController::class, 'verifyEmail']);
});
Route::post('reset-password', [ResetPasswordController::class, 'passwordReset']);

################################# End Authentication ##############################################

Route::middleware('auth:sanctum')->group(function () {
######################################## Profile ##########################################
    Route::get('me', [ProfileController::class, 'me']);
    Route::put('update-profile', [ProfileController::class, 'update']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('logout', [ProfileController::class, 'logout']);
    Route::delete('delete-profile', [ProfileController::class, 'destroy']);
######################################## End Profile #######################################

######################################## Task Management ##################################

    Route::apiResource('tasks', TaskController::class);

    Route::post('tasks/{task}/assign', [TaskController::class, 'assign']);

    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    
######################################## End Task Management ##############################

});

