<?php

namespace App\Http\Requests\Resources\Session;

use App\Models\Session\Session;
use Illuminate\Foundation\Http\FormRequest;

class StoreSessionVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Session $projectSession */
        $projectSession = $this->projectSession;

        /** @var null|string $secretToken */
        $secretToken = $this->header('x-devqaly-session-secret-token');

        if ($secretToken === null) {
            return false;
        }

        return $secretToken === $projectSession->secret_token;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'video' => 'required|file|mimetypes:video/webm'
        ];
    }
}
