<?php

namespace App\Http\Requests\Resources\Event;

use App\Models\Session\Event\EventNetworkRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GetEventsByRequestIdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var EventNetworkRequest $networkRequestEvent */
        $networkRequestEvent = $this->networkRequestEvent;

        /** @var User $loggedUser */
        $loggedUser = $this->user();

        return $loggedUser->can('view', [$networkRequestEvent->event->session]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
