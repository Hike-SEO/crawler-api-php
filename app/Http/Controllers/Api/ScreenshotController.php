<?php

namespace App\Http\Controllers\Api;

use App\Data\ScreenshotResult;
use App\Http\Controllers\Controller;
use App\Http\Requests\ScreenshotRequest;
use App\Services\CrawlService;

class ScreenshotController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(ScreenshotRequest $request): ScreenshotResult
    {
        return $this->crawlService->getScreenshot($request);
    }
}
