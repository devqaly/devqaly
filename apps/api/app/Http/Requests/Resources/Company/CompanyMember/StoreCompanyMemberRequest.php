<?php

namespace App\Http\Requests\Resources\Company\CompanyMember;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->company;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        return $loggedUser->can('create', [CompanyMember::class, $company]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'emails' => 'required|array|min:1',
            'emails.*' => 'distinct|email',
        ];
    }
}
