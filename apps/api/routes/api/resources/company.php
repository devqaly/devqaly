<?php

use App\Http\Controllers\Resources\CompanyController;
use App\Http\Controllers\Resources\CompanyMemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');

    Route::get('companies/{company}/members', [CompanyMemberController::class, 'index'])->name('companyMembers.index');
    Route::post('companies/{company}/members', [CompanyMemberController::class, 'store'])->name('companyMembers.store');
});
