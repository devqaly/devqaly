<?php

namespace app\Http\Controllers;

use App\Models\Company\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CypressController extends \Laracasts\Cypress\Controllers\CypressController
{
    public function login(Request $request): JsonResponse
    {
        $company = Company::factory()->create();

        $user = $company->createdBy;

        $user->load($request->input('load', []));

        $token = $user->createToken('Unnamed Token');

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
