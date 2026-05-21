<?php

namespace App\Http\Requests\UserRequests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
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
