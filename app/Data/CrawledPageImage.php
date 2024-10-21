<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawledPageImage extends Data
{
    public function __construct(
        public string $src,
        public ?string $alt = null,
    ) {}
}
