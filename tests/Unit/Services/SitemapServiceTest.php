<?php

namespace Tests\Unit\Services;

use App\Services\SitemapService;
use Http;
use Mockery\MockInterface;
use Tests\TestCase;
use vipnytt\SitemapParser;

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

        $result = $this->sitemapService->parse('https://example.com/sitemap.xml');

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

        $this->mock(SitemapParser::class, function (MockInterface $mock) {
            $mock->shouldReceive('parseRecursive')
                ->once()
                ->with('https://example.com/sitemap.xml');

            $mock->shouldReceive('getURLs')
                ->once()
                ->andReturn([
                    'https://example.com/sitemap.xml' => [
                        'namespaces' => [
                            'xhtml' => [],
                            'image' => [],
                            'video' => [],
                            'news' => [],
                        ],
                        'loc' => 'https://example.com/sitemap.xml',
                        'changefreq' => 'daily',
                        'priority' => '0.8',
                        'lastmod' => null,
                    ],
                ]);

            $mock->shouldReceive('getSitemaps')
                ->once()
                ->andReturn([
                    "https://laravel.com/sitemap_pages.xml" => [
                        "namespaces" => [],
                        "loc" => "https://laravel.com/sitemap_pages.xml",
                        "lastmod" => "2024-12-19T00:00:17+00:00",
                    ]
                ]);
        });

        $result = $this->sitemapService->parse('https://example.com/sitemap.xml');

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
