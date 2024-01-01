<?php

namespace App\services\Auth;

use App\Mail\Auth\SignupEmail;
use App\Models\Auth\RegisterToken;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\services\Resources\CompanyService;
use App\services\Resources\ProjectService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class RegisterTokenService
{
    private CompanyService $companyService;
    private ProjectService $projectService;

    public function __construct(CompanyService $companyService, ProjectService $projectService)
    {
        $this->companyService = $companyService;
        $this->projectService = $projectService;
    }

    public function createToken(
        Collection $data,
        bool $sendEmail = true,
        bool $hasOnboarding = false
    ): RegisterToken
    {
        $email = $data->get('email');

        if (User::where('email', $email)->exists()) {
            abort(Response::HTTP_FORBIDDEN, 'This email is already registered');
        }

        $registerToken = RegisterToken::create([
            'token' => $this->generateToken(),
            'email' => $email,
            'has_onboarding' => $hasOnboarding
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

        // Now that we have the user, we can update the `CompanyMember` to have
        // the real `member_id` that is attached to the `RegisterToken`
        /** @var CompanyMember|null $projectMember */
        CompanyMember::where('register_token_id', $registerToken->id)
            ->update([
                'member_id' => $user->id
            ]);

        // If the user registered first, the user won't have a company or a project.
        // First time users that doesn't have a Company and a Project will have the field
        // `has_onboarding` set to `true`. Since the onboarding process requires
        // User to have a Company and a Project, we will create those for him.
        $companyProject = ['company' => null, 'project' => null];

        if ($registerToken->has_onboarding) {
            $companyProject = $this->createCompanyAndProjectForFirstTimeUsers($user);
        }

        return collect([
            ...$companyProject,
            'user' => $user,
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

            $newRegisterToken = $this->createToken(collect(['email' => $token->email]));

            // A new token is created, and we need to reference this new token in `company_members` table
            // so that we can attach this user to the company whenever he uses the newly generated token.
            CompanyMember::where('register_token_id', $token->id)->update([
                'register_token_id' => $newRegisterToken->id
            ]);

            abort(Response::HTTP_FORBIDDEN, 'Current token have expired. We have sent a new token to the email associated with this token');
        }
    }

    private function createCompanyAndProjectForFirstTimeUsers(User $user): array
    {
        $company = $this->companyService->createCompany(
            data: collect(['name' => sprintf("%s's Company", $user->first_name)]),
            createdBy: $user
        );

        $project = $this->projectService->createProject(
            data: collect(['title' => sprintf("%s's Project", $user->first_name)]),
            creator: $user,
            company: $company,
        );

        return ['company' => $company, 'project' => $project];
    }
}
