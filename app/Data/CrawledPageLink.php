<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawledPageLink extends Data
{
    public function __construct(
        public string $url,
        public ?string $rel,
        public CrawledPageAnchor $anchor,
    ) {}
}
