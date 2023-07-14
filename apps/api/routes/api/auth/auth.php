<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('login', [LoginController::class, 'login'])->name('authentication.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('authentication.logout');
});

Route::post('resetPassword/link', [ResetPasswordController::class, 'requestPasswordResetLink']);

Route::post('/resetPassword', [ResetPasswordController::class, 'resetPassword'])
    ->name('password.update');

// This needs to be here so that we can still use Laravel's password reset
// feature without having to create our own password handlers
Route::get('/resetPassword/{token}', [ResetPasswordController::class, 'resetPasswordView'])
    ->name('password.reset');
