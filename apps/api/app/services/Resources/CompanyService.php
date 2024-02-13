<?php

namespace App\services\Resources;

use App\Models\Auth\RegisterToken;
use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\services\Auth\RegisterTokenService;
use App\services\SubscriptionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Exceptions\IncompletePayment;

class CompanyService
{
    public function createCompany(Collection $data, User $createdBy, array $customerOptionsMetadata = []): Company
    {
        DB::beginTransaction();

        try {
            /** @var Company $company */
            $company = Company::create([
                'name' => $data->get('name'),
                'created_by_id' => $createdBy->id
            ]);

            if (!config('devqaly.isSelfHosting')) {
                $this->createCustomOnStripe($company);
                $this->addCompanyTrial($company);
            }

            DB::commit();

            return $company;
        } catch (IncompletePayment $e) {
            DB::rollBack();

            Log::critical(sprintf('Failed in creating company [%s] with error: %s', $data->get('name'), $e->getMessage()));

            throw new \Exception($e);
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \Exception($e);
        }
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
        $registerTokens = RegisterToken::query()
            ->whereIn('email', $unregisteredUsersEmails)
            ->where('revoked', false)
            ->whereNull('used_at')
            ->get();

        $registerTokenService = app()->make(RegisterTokenService::class);

        /** @var string $email */
        foreach ($unregisteredUsersEmails as $email) {
            /** @var RegisterToken|null $oldRegisterToken */
            $oldRegisterToken = $registerTokens->first(fn(RegisterToken $r) => $r->email === $email);

            // 1. The current email has no token associated to it?
            // YES: The user is being invited for the first time.
            //      THEN: Create a register token and send the sign-up email
            if ($oldRegisterToken === null) {
                $newRegisterToken = $registerTokenService->createToken(collect(['email' => $email]), false);

                CompanyMember::create([
                    'company_id' => $company->id,
                    'member_id' => null,
                    'register_token_id' => $newRegisterToken->id,
                    'invited_by_id' => $invitedBy->id,
                ]);

                $registerTokenService->sendEmail($email, $newRegisterToken);

                continue;
            }

            // 2. Is the current register token assigned to the company that is inviting the user?
            // YES: It means the user is being re-invited
            //      THEN: Extend the token and re-send the email
            $isTokenAssignedToCompany = CompanyMember::query()
                ->where('register_token_id', $oldRegisterToken->id)
                ->whereNull('member_id')
                ->where('company_id', $company->id)
                ->exists();

            $oldRegisterToken->created_at = now();
            $oldRegisterToken->updated_at = now();
            $oldRegisterToken->save();

            if ($isTokenAssignedToCompany) {
                $registerTokenService->sendEmail($email, $oldRegisterToken);

                continue;
            }


            // 2. Is the current register token assigned to the company that is inviting the user?
            // NO: It means the user is being invited to a new company
            //      THEN: Extend the token and add the register token as a company member
            CompanyMember::create([
                'company_id' => $company->id,
                'member_id' => null,
                'register_token_id' => $oldRegisterToken->id,
                'invited_by_id' => $invitedBy->id,
            ]);
        }
    }

    private function createCustomOnStripe(Company $company): void
    {
        $company->createOrGetStripeCustomer([
            'email' => $company->createdBy->email,
            'name' => $company->name
        ]);
    }

    private function addCompanyTrial(Company $company): void
    {
        $company
            ->newSubscription(
                SubscriptionService::SUBSCRIPTION_GOLD_NAME,
                config('stripe.products.gold.prices.monthly')
            )
            // Quantity is necessary to set to null on metered plans
            // @see https://stackoverflow.com/a/64613077/4581336
            ->quantity(null)
            ->trialDays(SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS)
            ->create();
    }
}
