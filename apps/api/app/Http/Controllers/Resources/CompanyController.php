<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Company\CreateCompanyRequest;
use App\Http\Resources\Resources\CompanyResource;
use App\services\Resources\CompanyService;
use Illuminate\Http\Request;

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
}
