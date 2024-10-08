<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawlData extends Data
{
    /**
     * @param array<int,string> $h1s
     * @param array<int,string> $h2s
     * @param array<int,string> $h3s
     */
    public function __construct(
        public string $url,
        public array $h1s,
        public array $h2s,
        public array $h3s,
        public array $p,
        public ?CrawlDataMetrics $metrics = null,
    ) {}
}
