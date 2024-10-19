<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CrawledPageMetric extends Data
{
    public function __construct(
        public int $pageSize,
        /** @var null|DataCollection<int, CrawledPageAsset> */
        public ?DataCollection $assets,
    ) {}
}
