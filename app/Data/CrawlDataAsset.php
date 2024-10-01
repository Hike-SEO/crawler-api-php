<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class CrawlDataAsset extends Data
{
    public function __construct(
        public string $name,
        public float $startTime,
        public float $duration,
        public float $responseEnd,
        public string $fileType,
        public bool $internal,
        public int $transferSize,
        public int $status,
    ) {}
}
