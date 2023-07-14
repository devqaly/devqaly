<?php

use App\Http\Controllers\Resources\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('users/me', [UserController::class, 'me'])
        ->name('users.me');
});
