<?php

namespace App\Http\Controllers\Api;

use App\Data\SitemapData;
use App\Http\Controllers\Controller;
use App\Services\SitemapService;
use Illuminate\Http\JsonResponse;

class SitemapController extends Controller
{
    public function __construct(
        private readonly SitemapService $sitemapService,
    ) {}

    public function __invoke(SitemapData $request): JsonResponse
    {
        $websiteUrl = parse_url($request->websiteUrl, PHP_URL_SCHEME)
            .'://'.parse_url($request->websiteUrl, PHP_URL_HOST);

        $sitemapUrls = $this->sitemapService->fetch($websiteUrl);

        return response()->json($sitemapUrls);
    }
}
