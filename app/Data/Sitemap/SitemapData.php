<?php

namespace App\Data\Sitemap;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class SitemapData extends Data
{
    public function __construct(
        #[DataCollectionOf(SitemapIndices::class)]
        public array $indices,
        #[DataCollectionOf(SitemapUrls::class)]
        public array $urls,
    ) {}
}
