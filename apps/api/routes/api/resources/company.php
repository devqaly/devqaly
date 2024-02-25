<?php

use App\Http\Controllers\Resources\CompanyController;
use App\Http\Controllers\Resources\CompanyMemberController;
use App\Http\Controllers\Subscription\CompanySubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');

    Route::get('companies/{company}/stripe/portal', [CompanyController::class, 'getStripeCustomerPortal']);
    Route::get('companies/{company}/members', [CompanyMemberController::class, 'index'])->name('companyMembers.index');
    Route::post('companies/{company}/members', [CompanyMemberController::class, 'store'])->name('companyMembers.store');
    Route::delete('companies/{company}/members/{companyMember}', [CompanyMemberController::class, 'destroy'])->name('companyMembers.destroy');
    Route::put('companies/{company}/billingDetails', [CompanyController::class, 'updateBillingDetails'])->name('company.updateBillingDetails');
    Route::put('companies/{company}/subscription', [CompanySubscriptionController::class, 'updateCompanySubscription'])->name('company.subscription.update');
});
