<?php

namespace App\Http\Controllers\Auth;

use App\Events\MixpanelEventCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendEmailSignUpRequest;
use App\Http\Requests\Auth\StoreRegisterTokenRequest;
use App\Http\Requests\Auth\UpdateRegisterTokenRequest;
use App\Models\Auth\RegisterToken;
use App\services\Auth\RegisterTokenService;
use App\services\MixpanelService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function collect;
use function response;

class RegisterTokenController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreRegisterTokenRequest $request, RegisterTokenService $registerTokenService): JsonResponse
    {
        $registerTokenService->createToken(
            data: collect($request->validated()),
            hasOnboarding: true,
            sendMixpanelEvent: true,
        );

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function show(RegisterToken $registerToken)
    {
        //
    }

    public function update(
        UpdateRegisterTokenRequest $request,
        RegisterToken $registerToken,
        RegisterTokenService $registerTokenService
    ): JsonResponse
    {
        $data = $registerTokenService->completeRegistration(collect($request->validated()), $registerToken);

        $response = [
            'data' => [
                'user' => [
                    'email' => $data->get('user')->email,
                ],
                'registerToken' => [
                    'hasOnboarding' => $registerToken->has_onboarding,
                ]
            ]
        ];

        if ($data->get('project') && $data->get('company')) {
            $response['data']['company']['id'] = $data->get('company')->id;
            $response['data']['project']['id'] = $data->get('project')->id;
        }

        return response()->json($response);
    }

    public function destroy(RegisterToken $registerToken)
    {
        //
    }

    public function resendEmail(ResendEmailSignUpRequest $request, RegisterTokenService $registerTokenService): JsonResponse
    {
        $email = $request->get('email');
        $registerToken = RegisterToken::byEmail($email)
            ->whereNull('used_at')
            ->where('revoked', false)
            ->first();

        if ($registerToken) {
            $registerToken->update(['revoked' => 1]);

            $registerTokenService->createToken(
                data: collect(['email' => $email]),
                hasOnboarding: $registerToken->has_onboarding,
                sendMixpanelEvent: false
            );

            MixpanelEventCreated::dispatch('register-token-resend-email', MixpanelService::getBaseData(request()), $email);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

