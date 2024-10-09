<?php

namespace Tests\Unit\Services;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Requests\SingleCrawlRequest;
use App\Observers\SimpleCrawlObserver;
use App\Services\CrawlService;
use Mockery\MockInterface;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;
use Spatie\Crawler\CrawlQueues\CrawlQueue;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class CrawlServiceTest extends TestCase
{
    private CrawlService $crawlService;

    private MockInterface $crawler;

    private MockInterface $browsershot;

    private MockInterface $simpleObserver;

    private MockInterface $crawlDataFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crawlService = app(CrawlService::class);
        $this->browsershot = $this->partialMock(Browsershot::class);
        $this->crawler = $this->partialMock(Crawler::class);
        $this->simpleObserver = $this->partialMock(SimpleCrawlObserver::class);
        $this->crawlDataFactory = $this->partialMock(CrawlDataFactory::class);
    }

    public function test_single_crawl(): void
    {
        $url = 'https://hikeseo.co/';
        $request = new SingleCrawlRequest($url, performance: false);

        $this->browsershot->expects('setUrl')
            ->with($url);

        $this->browsershot->expects('setOption')
            ->with('waitUntil', $request->waitUntil);

        $crawledPage = CrawledPage::from([
            'url' => $url,
        ]);

        $this->simpleObserver->expects('getCrawlData')
            ->andReturn($crawledPage);

        $this->browsershot->expects('redirectHistory')
            ->andReturn(null);

        $this->browsershot->shouldNotReceive('evaluate');
        $this->crawlDataFactory->shouldNotReceive('parsePerformance');

        $this->crawler->shouldReceive('setCrawlerObserver')
            ->withArgs(function (CrawlObserver $crawlObserver) {
                $this->assertInstanceOf(SimpleCrawlObserver::class, $crawlObserver);

                return true;
            })
            ->andReturnSelf();

        $this->crawler->shouldReceive('setCrawlQueue')
            ->withArgs(function (CrawlQueue $crawlQueue) {
                $this->assertInstanceOf(ArrayCrawlQueue::class, $crawlQueue);

                return true;
            })
            ->andReturnSelf();

        $this->crawler->shouldReceive('setTotalCrawlLimit')
            ->with(1)
            ->andReturnSelf();

        $this->crawler->shouldReceive('startCrawling')
            ->with($url);

        $result = $this->crawlService->singleCrawlUrl($request);

        $this->assertEquals($crawledPage, $result);
    }

    public function test_single_crawl_with_redirects(): void {}

    public function test_single_crawl_with_performance(): void {}

    public function test_single_crawl_failed_response(): void {}

    public function test_single_crawl_no_crawl_data(): void {}
}
