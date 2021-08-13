<?php

use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/docs/index.html');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->name('verification.verify');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

Route::view('/successful-auth-action', 'auth.success-action')->name('auth.action.success');

