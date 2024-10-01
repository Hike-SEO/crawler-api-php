<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawlData extends Data
{
    public function __construct(
        public string $url,
        public array $h1s,
        public array $h2s,
        public array $h3s,
        public array $p,
        public ?CrawlDataMetrics $metrics = null,
    ) {}
}
