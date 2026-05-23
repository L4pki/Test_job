<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizationUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
            ],
            'password' => [
                'required',
                'string',
                'min:5',
                'max:20',
            ]
        ];
    }
}
