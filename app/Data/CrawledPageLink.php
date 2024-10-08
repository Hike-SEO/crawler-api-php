<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawledPageLink extends Data
{
    /**
     * @param array<string, string|null> $anchor
     */
    public function __construct(
        public string $url,
        public ?string $rel = null,
        public array $anchor = [],
    ) {}
}
