<?php

namespace App\services\Resources;

use App\Enum\Company\CompanyBlockedReasonEnum;
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
use Symfony\Component\HttpFoundation\Response;

class CompanyService
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function createCompany(Collection $data, User $createdBy, array $customerOptionsMetadata = []): Company
    {
        DB::beginTransaction();

        try {
            $fields = collect([
                'name' => $data->get('name'),
                'created_by_id' => $createdBy->id,
            ])
                ->when(!config('devqaly.isSelfHosting'), function (Collection $collection) {
                    $collection->put(
                        'trial_ends_at',
                        now()->addDays(SubscriptionService::SUBSCRIPTION_INITIAL_TRIAL_DAYS)
                    );
                });

            /** @var Company $company */
            $company = Company::create($fields->toArray());

            if (!config('devqaly.isSelfHosting')) {
                $this->createCustomOnStripe($company);
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

        $unregisteredUsersEmails = $emails->diff($registeredUsers->pluck('email'));

        $this->canInviteUsers($data->get('emails'), $company);

        foreach ($registeredUsers as $registeredUser) {
            CompanyMember::create([
                'company_id' => $company->id,
                'member_id' => $registeredUser->id,
                'register_token_id' => $oldRegisterToken?->id ?? null,
                'invited_by_id' => $invitedBy->id,
            ]);
        }

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

        $this->reportUsageForMembers($company);
    }

    public function removeUsersFromCompany(CompanyMember $companyMember): void
    {
        /** @var Company $totalNumberMembers */
        $company = $companyMember->company;

        $totalNumberMembers = $company->members()->count();

        if ($totalNumberMembers === 1) {
            abort(Response::HTTP_FORBIDDEN, 'You must have at least 1 members in the company');
        }

        $this->destroyUsersFromCompany($companyMember);
        $this->removeBlockedReasons($company);
        $this->reportUsageForMembers($company);
    }

    public function updateCompanyBillingDetails(Collection $data, Company $company): Company
    {
        $company->update([
            'billing_contact' => $data->get('billingContact', $company->billing_contact),
            'invoice_details' => $data->get('invoiceDetails', $company->invoice_details),
        ]);

        return $company;
    }

    public function createCustomOnStripe(Company $company, array $options = []): void
    {
        if (config('devqaly.isSelfHosting')) return;

        $company->createOrGetStripeCustomer(array_merge($options, [
            'email' => $company->createdBy->email,
            'name' => $company->name
        ]));
    }

    private function canInviteUsers(array $newUsers, Company $company): void
    {
        if (config('devqaly.isSelfHosting')) return;

        if ($this->subscriptionService->isPayingCustomer($company)) return;

        $numberCompanyMembers = $company->members()->count();
        $numberMembersBeingInvited = count($newUsers);
        $totalNumberMembers = ($numberCompanyMembers + $numberMembersBeingInvited);

        if ($totalNumberMembers > SubscriptionService::MAXIMUM_NUMBER_MEMBERS_FREE_PLAN_PER_COMPANY) {
            abort(Response::HTTP_FORBIDDEN, "Your plan does not support the amount of $totalNumberMembers members");
        }
    }

    private function reportUsageForMembers(Company $company): void
    {
        if (config('devqaly.isSelfHosting')) return;

        if ($this->subscriptionService->isSubscribedToGoldPlan($company)) {
            $company->subscription()->reportUsageFor(
                $this->subscriptionService->getGoldMonthlyPricingId(),
                $company->members()->count()
            );

            $company->last_time_reported_usage_to_stripe = now();
            $company->save();

            return;
        }

        if ($this->subscriptionService->isSubscribedToEnterprisePlan($company)) {
            $company->subscription()->reportUsageFor(
                $this->subscriptionService->getEnterpriseMonthlyPricingId(),
                $company->members()->count()
            );

            $company->last_time_reported_usage_to_stripe = now();
            $company->save();
        }
    }

    private function destroyUsersFromCompany(CompanyMember $companyMember): void
    {
        CompanyMember::query()
            ->where('company_id', $companyMember->company->id)
            ->when($companyMember->member_id, function ($query) use ($companyMember) {
                $query->where('member_id', $companyMember->member_id);
            })
            ->when($companyMember->register_token_id, function ($query) use ($companyMember) {
                $query->where('register_token_id', $companyMember->register_token_id);
            })
            ->delete();

        if ($companyMember->register_token_id) {
            RegisterToken::query()
                ->where('id', $companyMember->register_token_id)
                ->delete();
        }
    }

    private function removeBlockedReasons(Company $company): void
    {
        if (config('devqaly.isSelfHosting')) return;

        if (is_null($company->blocked_reasons)) return;

        if (count($company->blocked_reasons) < 1) return;

        if (!$this->subscriptionService->hasMoreMembersThanAllowedOnFreePlan($company)) {
            $company->blocked_reasons = collect($company->blocked_reasons)
                ->filter(function (array $reason) {
                    return $reason['reason'] !== CompanyBlockedReasonEnum::TRIAL_FINISHED_AND_HAS_MORE_MEMBERS_THAN_ALLOWED_ON_FREE_PLAN->value;
                });

            $company->save();
        }
    }
}
