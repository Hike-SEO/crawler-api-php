<?php

namespace App\Data\Sitemap;

use Spatie\LaravelData\Data;

class SitemapIndices extends Data
{
    public function __construct(
        public array $urls,
    ) {}
}
