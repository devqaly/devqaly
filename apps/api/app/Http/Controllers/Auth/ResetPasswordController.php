<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function requestPasswordResetLink(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            Log::info(
                sprintf(
                    'Tried to reset link to a user with email %s and it failed with [%s]',
                    $request->get('email'),
                    $status
                )
            );
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // This needs to be here so that we can still use Laravel's password reset
    // feature without having to create our own password handlers
    public function resetPasswordView(): JsonResponse
    {
        return response()->json(null);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            Log::info(
                sprintf(
                    'Tried to update password to a user with email %s and it failed with [%s]',
                    $request->get('email'),
                    $status
                )
            );

            return response()->json(['result' => $status], Response::HTTP_BAD_REQUEST);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
