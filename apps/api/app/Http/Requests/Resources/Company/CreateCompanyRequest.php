<?php

namespace App\Http\Requests\Resources\Company;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $this->user();

        return $loggedUser->can('create', [Company::class]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:1'
        ];
    }
}
