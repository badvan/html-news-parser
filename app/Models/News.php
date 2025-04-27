<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_story',
        'full_story',
        'images',
        'source_url',
        'template',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
