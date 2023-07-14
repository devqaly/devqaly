<?php

use App\Http\Controllers\Auth\RegisterTokenController;
use Illuminate\Support\Facades\Route;

// @TODO: add this back when we allow users to sign in
//Route::apiResource('registerTokens', RegisterTokenController::class)->only(['store', 'update']);
Route::apiResource('registerTokens', RegisterTokenController::class)->only(['update']);

Route::post('registerTokens/resendEmail', [RegisterTokenController::class, 'resendEmail'])
    ->middleware('throttle:re-send-registration-email')
    ->name('registerTokens.resendEmail');
