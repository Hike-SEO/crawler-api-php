<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Services\SitemapService;
use Tests\TestCase;

class SitemapControllerTest extends TestCase
{
    public function test_example()
    {
        app(SitemapService::class)->fetch('https://laravel.com/sitemap.xml');
    }
}
