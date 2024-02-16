<?php

namespace App\Http\Resources\Resources;

use App\services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return collect([
            'id' => $this->id,
            'name' => $this->name,
            'createdAt' => $this->created_at,
        ])
            ->when(!config('devqaly.isSelfHosting'), function (Collection $collection) {
                $subscription = $this->subscription();

                $collection->put('subscription', $subscription ? [
                    'endsAt' => $subscription->ends_at,
                    'createdAt' => $subscription->created_at,
                    'status' => $subscription->stripe_status,
                    'trialEndsAt' => $subscription->trial_ends_at,
                    'planName' => $this->getPlanName($subscription)

                ] : null);

                $collection->put('trialEndsAt', $this->trial_ends_at);
                $collection->put('paymentMethodType', $this->pm_type);
                $collection->put('paymentLastFourDigits', $this->pm_last_four);
                $collection->put('billingContact', $this->billing_contact);
                $collection->put('invoiceDetails', $this->invoice_details);
            })
            ->toArray();
    }

    private function getPlanName(Subscription $subscription): string
    {
        /** @var SubscriptionService $subscriptionService */
        $subscriptionService = app()->make(SubscriptionService::class);

        if ($subscription->hasProduct($subscriptionService->getEnterpriseProductId())) {
            return 'enterprise';
        }

        if ($subscription->hasProduct($subscriptionService->getGoldProductId())) {
            return 'gold';
        }

        return 'free';
    }
}
