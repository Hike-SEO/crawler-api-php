<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RobotsService
{
    public function getRobotsTxt(string $websiteUrl): string
    {
        $robotsUrl = parse_url($websiteUrl, PHP_URL_SCHEME)
            .'://'.parse_url($websiteUrl, PHP_URL_HOST);

        return Http::throw()
            ->timeout(3)
            ->withOptions(['allow_redirects' => ['max' => 2]])
            ->get("{$robotsUrl}/robots.txt")
            ->body();
    }
}
