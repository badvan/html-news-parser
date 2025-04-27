<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    public function store(array $data): News
    {
        return News::create($data);
    }
}
