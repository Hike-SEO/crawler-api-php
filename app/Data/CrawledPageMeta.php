<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawledPageMeta extends Data
{
    public function __construct(
        public string $property,
        public string $content,
    ) {}
}
