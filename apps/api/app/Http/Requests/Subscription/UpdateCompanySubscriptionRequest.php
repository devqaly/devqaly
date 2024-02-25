<?php

namespace App\Http\Requests\Subscription;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanySubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (config('devqaly.isSelfHosting')) return false;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        /** @var Company $company */
        $company = $this->company;

        return $loggedUser->can('update', $company);
    }

    public function rules(): array
    {
        return [
            'newPlan' => [
                'required',
                Rule::in(['free', 'gold'])
            ]
        ];
    }
}
