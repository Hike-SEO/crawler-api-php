<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class SimilarPage extends Data
{
    public function __construct(
        public string $url,
        public int|float $percentage
    ) {}
}
