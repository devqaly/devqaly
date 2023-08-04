<?php

use App\Http\Controllers\Resources\SessionController;
use App\Http\Controllers\Resources\SessionEventController;
use Illuminate\Support\Facades\Route;

Route::post('sessions/{projectSession}/events', [SessionEventController::class, 'store'])->name('sessions.events.store');

Route::post('sessions/{projectSession}/video', [SessionController::class, 'storeVideo'])->name('sessions.video.store');

Route::get('sessions/{session}/video', [SessionController::class, 'streamVideo'])->name('sessions.streamVideo');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('sessions/{session}', [SessionController::class, 'show'])->name('sessions.show');

    Route::get('sessions/{projectSession}/events', [SessionEventController::class, 'index'])->name('sessions.events.index');

    Route::post('sessions/{session}/assign', [SessionController::class, 'assignSessionToLoggedUser'])
        ->name('sessions.assignSessionToLoggedUser');
});
