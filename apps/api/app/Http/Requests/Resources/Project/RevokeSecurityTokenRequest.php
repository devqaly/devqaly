<?php

namespace App\Http\Requests\Resources\Project;

use App\Models\Project\Project;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RevokeSecurityTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $loggedUser */
        $loggedUser = $this->user();

        /** @var Project $project */
        $project = $this->project;

        return $loggedUser->can('view', $project);
    }

    public function rules(): array
    {
        return [];
    }
}
