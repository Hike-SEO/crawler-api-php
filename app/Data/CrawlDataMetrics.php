<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CrawlDataMetrics extends Data
{
    public function __construct(
        public int $pageSize,
        public CrawlDataTimings $timings,
        /** @var DataCollection<int, CrawledPageAsset> $assets */
        public DataCollection $assets,

    ) {}
}
