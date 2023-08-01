<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\UserCompany\IndexLoggedUserCompaniesRequest;
use App\Http\Resources\Resources\CompanyResource;
use App\Models\User;
use App\services\Resources\UserCompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserCompanyController extends Controller
{
    public function index(
        IndexLoggedUserCompaniesRequest $request,
        User $user,
        UserCompanyService $service
    ): AnonymousResourceCollection
    {
        $companies = $service->listUserCompanies(
            collect($request->validated()),
            $user,
        );

        return CompanyResource::collection($companies);
    }

    public function store(Request $request)
    {
        //
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
