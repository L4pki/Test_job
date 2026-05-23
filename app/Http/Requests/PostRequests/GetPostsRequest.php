<?php

namespace App\Http\Requests\PostRequests;

use Illuminate\Foundation\Http\FormRequest;

class GetPostsRequest extends FormRequest
{
     public function rules()
     {
         return [
             'limit' => [
                 'nullable',
                 'integer',
                 'min:1',
                 'max:100',
             ],
             'offset' => [
                 'nullable',
                 'integer',
                 'min:0',
             ],
             'sortBy' => [
                 'nullable',
                 'string',
                 'in:date,title',
             ],
             'sortOrder' => [
                 'nullable',
                 'string',
                 'in:asc,desc',
                 'required_with:sortBy',
             ],
             'date_from' => [
                 'nullable',
                 'date',
                 'date_format:Y-m-d',
                 'before_or_equal:date_to',
             ],
             'date_to' => [
                 'nullable',
                 'date',
                 'date_format:Y-m-d',
                 'after_or_equal:date_from',
             ],
        ];
    }
}
