<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['refresh_token_id', 'access_token_id'])]
class TokenConnection extends Model
{

}
