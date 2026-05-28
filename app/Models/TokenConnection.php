<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

class TokenConnection extends Model
{
    protected $fillable = [
        'refresh_token_id',
        'access_token_id'
    ];
}
