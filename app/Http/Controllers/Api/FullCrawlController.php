<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FullCrawlRequest;
use App\Models\FullCrawl;
use App\Models\Website;
use App\Services\CrawlService;

class FullCrawlController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(FullCrawlRequest $request): FullCrawl
    {
        $website = Website::findOrFail($request->websiteId);

        return $this->crawlService->startFullCrawl($website, $request);
    }
}
