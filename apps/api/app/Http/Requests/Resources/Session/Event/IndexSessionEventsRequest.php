<?php

namespace App\Http\Requests\Resources\Session\Event;

use App\Rules\OrderRule;
use App\Traits\UsesPaginate;
use Illuminate\Foundation\Http\FormRequest;

class IndexSessionEventsRequest extends FormRequest
{
    use UsesPaginate;

    public function authorize(): bool
    {
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
            ...$this->paginationValidation(500),
            'startCreatedAt' => 'date',
            'endCreatedAt' => 'date',
            'createdAtOrder' => ['string', new OrderRule],
        ];
    }
}
