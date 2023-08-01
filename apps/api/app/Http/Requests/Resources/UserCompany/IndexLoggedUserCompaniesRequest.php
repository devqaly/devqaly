<?php

namespace App\Http\Requests\Resources\UserCompany;

use App\Models\User;
use App\Traits\UsesPaginate;
use Illuminate\Foundation\Http\FormRequest;

class IndexLoggedUserCompaniesRequest extends FormRequest
{
    use UsesPaginate;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $loggedUser = $this->user();

        /** @var User $user */
        $user = $this->user;

        return $user->id === $loggedUser->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return $this->paginationValidation();
    }
}
