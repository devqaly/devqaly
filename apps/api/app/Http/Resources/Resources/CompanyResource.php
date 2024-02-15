<?php

namespace App\Http\Resources\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
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
                ] : null);

                $collection->put('trialEndsAt', $this->trial_ends_at);
                $collection->put('paymentMethodType', $this->pm_type);
                $collection->put('paymentLastFourDigits', $this->pm_last_four);
            })
            ->toArray();
    }
}
