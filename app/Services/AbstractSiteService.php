<?php

namespace App\Services;

use App\Models\NewsSite;

abstract class AbstractSiteService
{
    protected int $siteId;
    protected NewsSite $site;

    public function __construct(int $siteId)
    {
        $this->siteId = $siteId;
        $this->site = NewsSite::findOrFail($siteId);
    }

    public function getSite(): NewsSite
    {
        return $this->site;
    }

    public function getBaseUrl(): string
    {
        return $this->site->base_url;
    }

    public function getTemplate(): array
    {
        return json_decode(\Storage::get('parsers/' . $this->site->parser_template), true);
    }
}

