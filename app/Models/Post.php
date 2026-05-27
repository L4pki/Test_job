<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class Post extends Model
{
    use AsSource;

    protected $fillable = [
        'title',
        'text',
        'author_id'
    ];
}
