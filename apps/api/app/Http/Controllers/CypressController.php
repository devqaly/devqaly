<?php

namespace app\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CypressController extends \Laracasts\Cypress\Controllers\CypressController
{
    public function login(Request $request): JsonResponse
    {
        $attributes = $request->input('attributes', []);

        if (empty($attributes)) {
            $user = $this->factoryBuilder(
                $this->userClassName(),
                $request->input('state', [])
            )->create();
        } else {
            $user = app($this->userClassName())
                ->newQuery()
                ->where($attributes)
                ->first();

            if (!$user) {
                $user = $this->factoryBuilder(
                    $this->userClassName(),
                    $request->input('state', [])
                )->create($attributes);
            }
        }

        $user->load($request->input('load', []));

        $token = $user->createToken('Unnamed Token');

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
