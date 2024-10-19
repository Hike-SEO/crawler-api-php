<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class Url extends Data
{
    public function __construct(
        public string $fullUrl,
        public string $scheme,
        public string $host,
        public ?string $query,
        public ?string $path,
        public ?string $fragment,
    ) {}
}
