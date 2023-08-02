<?php

namespace App\Http\Requests\Resources\Project;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $this->user();

        /** @var Company $company */
        $company = $this->company;

        return $loggedUser->can('create', [Project::class, $company]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|min:2|max:255',
        ];
    }
}
