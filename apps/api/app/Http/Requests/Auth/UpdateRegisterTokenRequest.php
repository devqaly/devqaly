<?php

namespace App\Http\Requests\Auth;

use App\Enum\User\UserCurrentPositionEnum;
use App\services\Auth\RegisterTokenService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UpdateRegisterTokenRequest extends FormRequest
{
    public function authorize()
    {
        /** @var RegisterTokenService $registerTokenService */
        $registerTokenService = app()->make(RegisterTokenService::class);

        $token = $this->registerToken;

        if ($token->revoked || !is_null($token->used_at)) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid token');
        }

        $registerTokenService->validateRegisterToken($token);

        return true;
    }

    public function rules()
    {
        return [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'password' => 'required|string|min:8',
            'timezone' => 'required|timezone',
            'currentPosition' => [
                'required',
                Rule::enum(UserCurrentPositionEnum::class),
            ],
        ];
    }
}
