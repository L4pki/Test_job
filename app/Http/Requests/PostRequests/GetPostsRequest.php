<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;

class GetPostsRequest extends FormRequest
{
    public function rules():array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ]
        ];
    }
}
