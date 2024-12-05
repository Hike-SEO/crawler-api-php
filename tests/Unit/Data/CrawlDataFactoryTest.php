<?php

namespace Tests\Unit\Data;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class CrawlDataFactoryTest extends TestCase
{
    private CrawlDataFactory $crawlDataFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlDataFactory = app(CrawlDataFactory::class);
    }

    public function test_from_response(): void
    {
        $url = 'https://hikeseo.co';
        $html = file_get_contents(base_path('tests/Fixtures/Data/Factories/CrawlDataFactory/test.html'));
        $response = new Response(200, body: $html);

        $result = $this->crawlDataFactory->fromResponse($url, $response);

        $this->assertInstanceOf(CrawledPage::class, $result);
        $this->assertEquals($url, $result->url);
        $this->assertEquals('', $result->path);
        $this->assertEquals('Hike SEO', $result->title);
        $this->assertEquals('Here is some content. And some more content.', $result->content);
        $this->assertEquals(8, $result->word_count);
        $this->assertEquals('https://hikeseo.co', $result->canonical_link);
        $this->assertEquals('v1', $result->optimiser);
        $this->assertEquals('DIY SEO done easy.', $result->meta_desc);
        $this->assertEquals('index, follow', $result->meta_robots);
        $this->assertEquals('diy, seo', $result->meta_keywords);

        $this->assertEquals(['Hike SEO', 'Another title'], $result->h1_headings);
        $this->assertEquals(['Subtitle'], $result->h2_headings);
        $this->assertEquals(['Smaller title'], $result->h3_headings);

        $this->assertEquals([
            'https://hikeseo.co/about',
            'https://hikeseo.co/meet-the-team',
        ], collect($result->internal_links)->pluck('url')->all());

        $this->assertEquals([
            'https://facebook.com',
        ], collect($result->external_links)->pluck('url')->all());
        // TODO check other properties
    }
}
