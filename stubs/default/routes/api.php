<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/resend-verify-link', [EmailVerificationNotificationController::class, 'store']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

Route::post('/reset-password', [NewPasswordController::class, 'store']);

Route::middleware(['auth:sanctum', 'verified'])->group(function (){
    // Route::post('/user', [UserController::class, 'show']);
});
