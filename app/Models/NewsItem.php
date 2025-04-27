<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsItem extends Model
{
    protected $fillable = ['news_site_id', 'title', 'short_story', 'full_story', 'link'];
}
