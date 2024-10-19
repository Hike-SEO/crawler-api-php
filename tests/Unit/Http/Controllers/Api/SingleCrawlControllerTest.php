<?php

namespace Tests\Unit\Http\Controllers\Api;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Middleware\AuthenticateSecretKey;
use App\Http\Requests\SingleCrawlRequest;
use App\Observers\SimpleCrawlObserver;
use App\Services\Crawler;
use App\Services\CrawlService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\MockInterface;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;
use Spatie\Crawler\CrawlQueues\CrawlQueue;
use Tests\TestCase;

class SingleCrawlControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setApiKey('secret-key');
    }

    public function test_it_accepts_valid_url(): void
    {
        $this->mock(CrawlService::class)
            ->expects('singleCrawlUrl')
            ->andReturn(CrawledPage::from([
                'url' => 'https://example.com/',
            ]));

        $response = $this->withHeader('x-api-key', 'secret-key')
            ->postJson('/api/crawl/single', [
                'website_url' => 'https://example.com/',
                'wait_until' => 'domcontentloaded',
                'performance' => true,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'crawled_page' => [
                        'url' => 'https://example.com/',
                    ],
                ],
            ]);
    }

    public function test_it_returns_validation_error_for_invalid_url(): void
    {
        $response = $this->withHeader('x-api-key', 'secret-key')
            ->postJson('/api/crawl/single', [
                'website_url' => 'not_a_url',
                'wait_until' => 'domcontentloaded',
                'performance' => true,
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Please provide a valid URL',
            ]);
    }
}
