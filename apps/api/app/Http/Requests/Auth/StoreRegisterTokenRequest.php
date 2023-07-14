<?php

namespace App\Http\Requests\Auth;

use App\Models\Auth\RegisterToken;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegisterTokenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique((new RegisterToken())->getTable())->where(function ($query) {
                    return $query->where('revoked', 0);
                }),
                Rule::unique((new User())->getTable(), 'email'),
            ]
        ];
    }
}
