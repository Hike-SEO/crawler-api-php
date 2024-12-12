<?php

namespace App\Http\Controllers\Api;

use App\Data\Sitemap\SitemapRequest;
use App\Http\Controllers\Controller;
use App\Services\SitemapService;
use Illuminate\Http\JsonResponse;

class SitemapController extends Controller
{
    public function __construct(
        private readonly SitemapService $sitemapService,
    ) {}

    public function __invoke(SitemapRequest $request): JsonResponse
    {
        $sitemapUrl = parse_url($request->websiteUrl, PHP_URL_SCHEME)
            .'://'.parse_url($request->websiteUrl, PHP_URL_HOST);

        $sitemapData = $this->sitemapService->fetch($sitemapUrl);

        return response()->json($sitemapData);
    }
}
