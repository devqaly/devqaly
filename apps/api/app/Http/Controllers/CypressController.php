<?php

namespace app\Http\Controllers;

use App\Models\Company\Company;
use App\services\Resources\CompanyService;
use app\services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CypressController extends \Laracasts\Cypress\Controllers\CypressController
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function login(Request $request): JsonResponse
    {
        $company = Company::factory()->create();

        $user = $company->createdBy;

        $user->load($request->input('load', []));

        $token = $user->createToken('Unnamed Token');

        $this->companyService->createCustomOnStripe($company, [
            'metadata' => [
                'environment' => 'e2e'
            ]
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
