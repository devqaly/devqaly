<?php

namespace App\Http\Requests\Resources\Company;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyBillingDetailsMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $this->user();

        /** @var Company $company */
        $company = $this->company;

        return $loggedUser->can('update', $company);
    }

    public function rules(): array
    {
        return [
            'billingContact' => 'string|email|max:255',
            'invoiceDetails' => 'string|max:65535',
        ];
    }
}
