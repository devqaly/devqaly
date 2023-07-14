<?php

namespace App\Http\Requests\Resources\Project\Session;

use App\Models\Project\Project;
use App\Models\Session\Session;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Project $project */
        $project = $this->project;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        // @TODO: we need to check here that the projectKey is good
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'os' => 'required|string|max:255',
            'platformName' => 'required|string|max:255',
            'version' => 'required|string|max:255',
            'windowWidth' => 'required|integer|min:1|max:65535',
            'windowHeight' => 'required|integer|min:1|max:65535'
        ];
    }
}
