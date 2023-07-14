<?php

use App\Http\Controllers\Resources\CompanyMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('companies/{company}/members', [CompanyMemberController::class, 'index']);
    Route::post('companies/{company}/members', [CompanyMemberController::class, 'store']);
});
