<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RobotsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class RobotsController extends Controller
{
    public function __invoke(RobotsRequest $request): JsonResponse
    {
        $robotsUrl = parse_url($request->websiteUrl, PHP_URL_SCHEME)
            .'://'.parse_url($request->websiteUrl, PHP_URL_HOST);

        return response()->json([
            Http::get("{$robotsUrl}/robots.txt")->body(),
        ]);
    }
}
