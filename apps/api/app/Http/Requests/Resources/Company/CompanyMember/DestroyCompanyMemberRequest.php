<?php

namespace App\Http\Requests\Resources\Company\CompanyMember;

use Illuminate\Foundation\Http\FormRequest;

class DestroyCompanyMemberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'users' => 'array|min:1',
            'users.*' => 'string|uuid|distinct',
            'registerTokens' => 'array|min:1',
            'registerTokens.*' => 'string|uuid|distinct',
        ];
    }
}
