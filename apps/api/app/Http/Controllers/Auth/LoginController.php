<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(LoginRequest $request): Collection
    {
        $data = collect($request->validated());

        $user = User::where('email', $data->get('email'))
            ->first();

        if (! $user || ! Hash::check($data->get('password'), $user->password)) {
            abort(Response::HTTP_FORBIDDEN, 'Bad credentials');
        }

        $token = $user->createToken($data->get('tokenName'));

        return collect([
            'token' => [
                'plainTextToken' => $token->plainTextToken,
                'accessToken' => [
                    'name' => $token->accessToken->name,
                    'abilities' => $token->accessToken->abilities,
                ]
            ]
        ]);
    }
}
