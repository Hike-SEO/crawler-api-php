<?php

namespace App\Data\Factories;

use Spatie\LaravelData\Data;

class CrawlDataTimings extends Data
{
    public function __construct(
        public int $connectStart,
        public int $navigationStart,
        public int $loadEventEnd,
        public int $domLoading,
        public int $secureConnectionStart,
        public int $fetchStart,
        public int $domContentLoadedEventStart,
        public int $responseStart,
        public int $responseEnd,
        public int $domInteractive,
        public int $domainLookupEnd,
        public int $redirectStart,
        public int $requestStart,
        public int $unloadEventEnd,
        public int $unloadEventStart,
        public int $domComplete,
        public int $domainLookupStart,
        public int $loadEventStart,
        public int $domContentLoadedEventEnd,
        public int $redirectEnd,
        public int $connectEn,
    ) {}
}
