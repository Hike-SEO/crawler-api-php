<?php

namespace App\Data\Sitemap;

use Spatie\LaravelData\Data;

class SitemapUrls extends Data
{
    public function __construct(
        public array $urls,
    ) {}
}
