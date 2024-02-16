<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Company\CreateCompanyRequest;
use App\Http\Resources\Resources\CompanyResource;
use App\Models\Company\Company;
use App\services\Resources\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function index()
    {
        //
    }

    public function store(CreateCompanyRequest $request, CompanyService $companyService): CompanyResource
    {
        $loggedUser = $request->user();

        $company = $companyService->createCompany(
            collect($request->validated()),
            $loggedUser,
        );

        $companyService->addMemberToCompany(
            data: collect(['emails' => [$loggedUser->email]]),
            invitedBy: $loggedUser,
            company: $company,
            sendInvitationEmail: false,
        );

        return new CompanyResource($company);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function getStripeCustomerPortal(Request $request, Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        $this->validate($request, [
            'returnUrl' => 'required|url'
        ]);

        $returnUrl = $request->get('returnUrl');
        $host = parse_url($returnUrl)['host'];

        if (!in_array($host, ['app.devqaly.com', 'localhost', 'staging-app.devqaly.com'])) {
            abort(Response::HTTP_FORBIDDEN, 'Return URL must be a URL pointing to devqaly servers');
        }

        return response()->json([
            'data' => [
                'portalUrl' => $company->billingPortalUrl($returnUrl)
            ]
        ]);
    }
}
