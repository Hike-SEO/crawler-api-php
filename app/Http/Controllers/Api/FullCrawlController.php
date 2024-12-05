<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FullCrawlRequest;
use App\Http\Resources\FullCrawlResource;
use App\Models\Website;
use App\Services\CrawlService;
use Illuminate\Http\Resources\Json\JsonResource;

class FullCrawlController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(FullCrawlRequest $request): JsonResource
    {
        $website = Website::findOrFail($request->websiteId);

        $fullCrawl = $this->crawlService->startFullCrawl($website, $request);

        return FullCrawlResource::make($fullCrawl);
    }
}
