<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawledPageAnchor extends Data
{
    public function __construct(
        public string $nodeName,
        public string $content,
    ) {}
}
