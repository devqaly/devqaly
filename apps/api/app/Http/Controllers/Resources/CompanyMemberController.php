<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Company\CompanyMember\IndexCompanyMemberRequest;
use App\Http\Requests\Resources\Company\CompanyMember\StoreCompanyMemberRequest;
use App\Http\Resources\Resources\CompanyMemberResource;
use App\Models\Company\Company;
use App\services\Resources\Company\CompanyMemberService;
use App\services\Resources\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CompanyMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(
        IndexCompanyMemberRequest $request,
        Company $company,
        CompanyMemberService $service
    ): AnonymousResourceCollection
    {
        $members = $service->listMembers(collect($request->validated()), $company);

        return CompanyMemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreCompanyMemberRequest $request,
        Company $company,
        CompanyService $companyService
    ): JsonResponse
    {
        $companyService->addMemberToCompany(
            data: collect($request->validated()),
            invitedBy: $request->user(),
            company: $company,
            oldRegisterToken: null,
            sendInvitationEmail: true,
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
