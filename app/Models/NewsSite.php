<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsSite extends Model
{
    protected $fillable = ['name', 'base_url', 'parser_template'];
}
