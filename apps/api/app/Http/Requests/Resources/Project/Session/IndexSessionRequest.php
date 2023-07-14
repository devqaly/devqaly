<?php

namespace App\Http\Requests\Resources\Project\Session;

use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use App\Rules\OrderRule;
use App\Traits\UsesPaginate;
use Illuminate\Foundation\Http\FormRequest;

class IndexSessionRequest extends FormRequest
{
    use UsesPaginate;

    public function authorize(): bool
    {
        /** @var Project $project */
        $project = $this->project;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        return $loggedUser->can('viewAny', [Session::class, $project]);
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
            'createdAtOrder' => ['string', new OrderRule],
            'createdByName' => 'string',
            'os' => 'string',
            'platform' => 'string',
            'version' => 'string'
        ];
    }
}
