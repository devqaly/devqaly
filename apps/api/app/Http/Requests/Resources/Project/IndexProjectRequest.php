<?php

namespace App\Http\Requests\Resources\Project;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\User;
use App\Traits\UsesPaginate;
use Illuminate\Foundation\Http\FormRequest;

class IndexProjectRequest extends FormRequest
{
    use UsesPaginate;

    public function authorize(): bool
    {
        /** @var Company $company */
        $company = $this->company;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        return $loggedUser->can('viewAny', [Project::class, $company]);
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
            'loadUrls' => 'boolean',
            'title' => 'string'
        ];
    }
}
