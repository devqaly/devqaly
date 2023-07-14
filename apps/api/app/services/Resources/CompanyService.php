<?php

namespace App\services\Resources;

use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\services\Auth\RegisterTokenService;
use Illuminate\Support\Collection;

class CompanyService
{
    private RegisterTokenService $registerTokenService;

    public function __construct(RegisterTokenService $registerTokenService)
    {
        $this->registerTokenService = $registerTokenService;
    }

    public function createCompany(Collection $data, User $createdBy): Company
    {
        return Company::create([
            'name' => $data->get('name'),
            'created_by_id' => $createdBy->id
        ]);
    }

    public function addMemberToCompany(
        Collection    $data,
        User          $invitedBy,
        Company       $company,
        RegisterToken $oldRegisterToken = null,
        bool          $sendInvitationEmail = true
    ): void
    {
        $emails = collect($data->get('emails'));

        $registeredUsers = User::whereIn('email', $emails)->get();

        foreach ($registeredUsers as $registeredUser) {
            CompanyMember::create([
                'company_id' => $company->id,
                'member_id' => $registeredUser->id,
                'register_token_id' => $oldRegisterToken?->id ?? null,
                'invited_by_id' => $invitedBy->id,
            ]);
        }

        $unregisteredUsersEmails = $emails->diff($registeredUsers->pluck('email'));

        /** @var Collection $registerTokens */
        $registerTokens = RegisterToken::whereIn('email', $unregisteredUsersEmails)
            ->where('revoked', false)
            ->whereNull('used_at')
            ->get();

        /** @var string $email */
        foreach ($unregisteredUsersEmails as $email) {
            /** @var RegisterToken|null $oldRegisterToken */
            $oldRegisterToken = $registerTokens->first(fn(RegisterToken $r) => $r->email === $email);

            // In case the user was already invited to join the platform, we will revoke the last token
            $oldRegisterToken?->update(['revoked' => 1]);

            // We will create the RegisterToken for the user being invited.
            // Whenever the user finishes registration, we will attach
            // the user created to this CompanyMember.
            $newRegisterToken = $this->registerTokenService->createToken(collect(['email' => $email]), false);

            if ($oldRegisterToken === null) {
                CompanyMember::create([
                    'company_id' => $company->id,
                    'member_id' => null,
                    'register_token_id' => $newRegisterToken->id,
                    'invited_by_id' => $invitedBy->id,
                ]);
            } else {
                CompanyMember::where('register_token_id', $oldRegisterToken->id)
                    ->update(['register_token_id' => $newRegisterToken->id]);
            }

            if ($sendInvitationEmail) {
                $this->registerTokenService->sendEmail($email, $newRegisterToken);
            }
        }
    }
}
