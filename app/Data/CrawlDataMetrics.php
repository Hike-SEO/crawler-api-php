<?php

namespace App\Data;

use App\Data\Factories\CrawlDataTimings;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CrawlDataMetrics extends Data
{
    public function __construct(
        public CrawlDataTimings $timings,
        /** @var DataCollection<int, CrawlDataAsset> $assets */
        public DataCollection $assets,

    ) {}
}
