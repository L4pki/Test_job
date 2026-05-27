<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;

class PublicationPostRequest extends FormRequest
{

    protected function prepareForValidation(): void
    {
        if ($this->has('post')) {
            $this->merge($this->input('post'));
        }
    }

    public function rules()
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'text' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ]
        ];
    }
}
