<?php

use App\Http\Controllers\Resources\ProjectController;
use App\Http\Controllers\Resources\ProjectSessionController;
use Illuminate\Support\Facades\Route;

Route::post('projects/{project:project_key}/sessions', [ProjectSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('companies/{company}/projects', [ProjectController::class, 'store']);

    Route::get('projects/{project}', [ProjectController::class, 'show']);

    Route::get('projects/{project}/sessions', [ProjectSessionController::class, 'index']);

    Route::get('companies/{company}/projects', [ProjectController::class, 'index']);
});
