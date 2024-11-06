<?php

namespace Tests\Unit\Observers;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Observers\SimpleCrawlObserver;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Mockery\MockInterface;
use Tests\TestCase;

class SimpleCrawlObserverTest extends TestCase
{
    private SimpleCrawlObserver $observer;

    private MockInterface $crawlDataFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlDataFactory = $this->mock(CrawlDataFactory::class);
        $this->observer = app(SimpleCrawlObserver::class);
    }

    public function test_crawled(): void
    {
        $url = 'https://hikeseo.co';
        $response = new Response;

        $crawlData = CrawledPage::from([
            'url' => $url,
        ]);

        $this->crawlDataFactory->expects('fromResponse')
            ->with($url, $response)
            ->andReturn($crawlData);

        $this->observer->crawled(new Uri($url), $response);

        $this->assertEquals($crawlData, $this->observer->getCrawlData());
    }

    public function test_crawl_failed_with_response(): void
    {
        $url = 'https://hikeseo.co';
        $response = new Response;

        $exception = new RequestException('', new Request('GET', $url), $response);

        $crawlData = CrawledPage::from([
            'url' => $url,
        ]);

        $this->crawlDataFactory->expects('fromResponse')
            ->with($url, $response)
            ->andReturn($crawlData);

        $this->observer->crawlFailed(new Uri($url), $exception);

        $this->assertEquals($crawlData, $this->observer->getCrawlData());
    }

    public function test_crawl_failed_without_response(): void
    {
        $url = 'https://hikeseo.co';
        $errorMessage = 'Connection error';
        $exception = new RequestException($errorMessage, new Request('GET', $url));

        $this->crawlDataFactory->shouldNotReceive('fromResponse');

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage($errorMessage);

        $this->observer->crawlFailed(new Uri($url), $exception);
    }
}
