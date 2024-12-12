<?php

namespace Tests\Unit\Services;

use App\Services\SitemapService;
use Http;
use Tests\TestCase;

class SitemapServiceTest extends TestCase
{
    private SitemapService $sitemapService;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();

        $this->sitemapService = app(SitemapService::class);
    }

    public function test_fetch_sitemap()
    {
        $sitemapData = file_get_contents(base_path('tests/fixtures/Data/sitemaps/sitemap.xml'));

        Http::fake([
            'example.com/sitemap.xml' => Http::response($sitemapData, 200, [
                'Content-Type' => 'application/xml',
            ]),
        ]);

        $result = $this->sitemapService->fetch('https://example.com/sitemap.xml');

        $urls = [
            'https://example.com/about',
            'https://example.com/contact-us',
            'https://example.com/faq',
        ];

        $this->assertSame($urls, $result->urls);
    }

    public function test_fetch_indices_sitemap()
    {
        $sitemapIndicesData = file_get_contents(base_path('tests/fixtures/Data/sitemaps/sitemap-indices.xml'));
        $sitemapData = file_get_contents(base_path('tests/fixtures/Data/sitemaps/sitemap.xml'));

        Http::fake([
            'example.com/sitemap.xml' => Http::response($sitemapIndicesData, 200, [
                'Content-Type' => 'application/xml',
            ]),
            'https://example.com/sitemap_pages.xml' => Http::response($sitemapData, 200, [
                'Content-Type' => 'application/xml',
            ]),
        ]);

        $result = $this->sitemapService->fetch('https://example.com/sitemap.xml');

        $expectedIndices = [
            'https://example.com/sitemap_pages.xml' => [
                'https://example.com/about',
                'https://example.com/contact-us',
                'https://example.com/faq',
            ],
        ];

        $expectedUrls = [
            'https://example.com/about',
            'https://example.com/contact-us',
            'https://example.com/faq',
        ];

        $this->assertSame($expectedIndices, $result->indices);
        $this->assertSame($expectedUrls, $result->urls);
    }
}
