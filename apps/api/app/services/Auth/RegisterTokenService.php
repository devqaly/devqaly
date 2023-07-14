<?php

namespace App\services\Auth;

use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\Company\CompanyMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class RegisterTokenService
{
    public function createToken(Collection $data, bool $sendEmail = true): RegisterToken
    {
        $email = $data->get('email');

        if (User::where('email', $email)->exists()) {
            abort(Response::HTTP_FORBIDDEN, 'This email is already registered');
        }

        $registerToken = RegisterToken::create([
            'token' => $this->generateToken(),
            'email' => $email,
        ]);

        if ($sendEmail) {
            $this->sendEmail($email, $registerToken);
        }

        return $registerToken;
    }

    public function completeRegistration(Collection $data, RegisterToken $registerToken): Collection
    {
        $user = User::create([
            'first_name' => ucfirst($data->get('firstName')),
            'last_name' => ucfirst($data->get('lastName')),
            'timezone' => $data->get('timezone'),
            'password' => Hash::make($data->get('password')),
            'email' => $registerToken->email,
            'current_position' => $data->get('currentPosition'),
        ]);

        $registerToken->update(['used_at' => Carbon::now()]);

        // Check if this user has any pending invitations from projects to join.
        // In case it has, we need to replace the `member_id` with its' actual created user.
        /** @var CompanyMember|null $projectMember */
        $companyMember = CompanyMember::where('register_token_id', $registerToken->id)->first();
        $companyMember?->update(['member_id' => $user->id]);

        return collect([
            'user' => $user
        ]);
    }

    public function generateToken(): string
    {
        return bin2hex(random_bytes(20));
    }

    public function sendEmail(string $email, RegisterToken $registerToken): void
    {
        Mail::to($email)->queue(new SignupEmail($registerToken));
    }

    public function validateRegisterToken(?RegisterToken $token): void
    {
        if (is_null($token)) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid token');
        }

        if ($token->created_at->diffInDays(Carbon::now()) > 2) {
            $token->update(['revoked' => true]);

            $this->createToken(collect(['email' => $token->email]));

            abort(Response::HTTP_FORBIDDEN, 'Current token have expired. We have sent a new token to the email associated with this token');
        }
    }
}
