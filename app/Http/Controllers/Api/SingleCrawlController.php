<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SingleCrawlRequest;
use App\Services\CrawlService;
use Illuminate\Http\JsonResponse;

class SingleCrawlController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(SingleCrawlRequest $request): JsonResponse
    {
        $data = $this->crawlService->crawlUrl($request);

        return $data->toResponse(request());
    }
}