<?php

namespace App\Http\Controllers\Api;

use App\Data\CrawledPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\SingleCrawlRequest;
use App\Services\CrawlService;

class SingleCrawlController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(SingleCrawlRequest $request): CrawledPage
    {
        return $this->crawlService->singleCrawlUrl($request);
    }
}
