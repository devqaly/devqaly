<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\UpdateCompanySubscriptionRequest;
use App\Http\Resources\Resources\CompanyResource;
use App\Models\Company\Company;
use App\services\CompanySubscriptionService;

class CompanySubscriptionController extends Controller
{
    public function updateCompanySubscription(
        UpdateCompanySubscriptionRequest $request,
        Company $company,
        CompanySubscriptionService $companySubscriptionService
    ): CompanyResource
    {
        $companySubscriptionService->changeCompanyPlan($company, $request->get('newPlan'));

        $company->refresh();

        return new CompanyResource($company);
    }
}
