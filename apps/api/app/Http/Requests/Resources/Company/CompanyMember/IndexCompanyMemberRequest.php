<?php

namespace App\Http\Requests\Resources\Company\CompanyMember;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use App\Rules\OrderRule;
use App\Traits\UsesPaginate;
use Illuminate\Foundation\Http\FormRequest;

class IndexCompanyMemberRequest extends FormRequest
{
    use UsesPaginate;

    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $this->user();

        /** @var Company $company */
        $company = $this->company;

        return $loggedUser->can('viewAny', [CompanyMember::class, $company]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            ...$this->paginationValidation(),
            'memberName' => 'string',
            'invitedByName' => 'string',
            'orderByCreatedAt' => ['string', new OrderRule],
        ];
    }
}
