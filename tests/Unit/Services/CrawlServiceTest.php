<?php

namespace Tests\Unit\Services;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Data\ScreenshotResult;
use App\Http\Requests\PdfRequest;
use App\Http\Requests\ScreenshotRequest;
use App\Http\Requests\SingleCrawlRequest;
use App\Observers\SimpleCrawlObserver;
use App\Services\Crawler;
use App\Services\CrawlService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;
use Spatie\Crawler\CrawlQueues\CrawlQueue;
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
        $this->browsershot = $this->partialMock(Browsershot::class);
        $this->crawler = $this->mock(Crawler::class);
        $this->simpleObserver = $this->partialMock(SimpleCrawlObserver::class);
        $this->crawlDataFactory = $this->partialMock(CrawlDataFactory::class);
        $this->crawlService = app(CrawlService::class);

        $this->crawler
            ->expects('getBrowsershot')
            ->andReturn($this->browsershot);
    }

    protected function setupSingleCrawl(SingleCrawlRequest $request): void
    {
        $this->browsershot->expects('setUrl')
            ->with($request->websiteUrl);

        $this->browsershot->expects('setOption')
            ->with('waitUntil', $request->waitUntil);

        $this->crawler->expects('setCrawlObserver')
            ->withArgs(function (CrawlObserver $observer) {
                $this->assertInstanceOf(SimpleCrawlObserver::class, $observer);

                return true;
            })->andReturnSelf();

        $this->crawler->expects('setCrawlQueue')
            ->withArgs(function (CrawlQueue $queue) {
                $this->assertInstanceOf(ArrayCrawlQueue::class, $queue);

                return true;
            })->andReturnSelf();

        $this->crawler->expects('setTotalCrawlLimit')
            ->with(1)
            ->andReturnSelf();
    }

    protected function withoutPerformance(): void
    {
        $this->browsershot->shouldNotReceive('evaluate');
        $this->crawlDataFactory->shouldNotReceive('parsePerformance');
    }

    public function test_single_crawl(): void
    {
        $url = 'https://hikeseo.co/';
        $request = new SingleCrawlRequest($url, performance: false);

        $this->setupSingleCrawl($request);
        $this->withoutPerformance();

        $crawledPage = CrawledPage::from([
            'url' => $url,
        ]);

        $this->simpleObserver->expects('getCrawlData')
            ->andReturn($crawledPage);

        $this->browsershot->expects('redirectHistory')
            ->andReturn(null);

        $this->crawler->shouldReceive('startCrawling')
            ->with($url);

        $result = $this->crawlService->singleCrawlUrl($request);

        $this->assertEquals($crawledPage, $result);
    }

    public function test_single_crawl_with_redirects(): void
    {
        $url = 'https://hikeseo.co/';
        $redirectUrl = 'https://hikeseo.co/home';
        $request = new SingleCrawlRequest($url, performance: false);

        $this->setupSingleCrawl($request);
        $this->withoutPerformance();

        $this->simpleObserver->expects('getCrawlData')
            ->andReturn(null);

        $redirects = [
            [
                'url' => $url,
                'status' => 301,
            ],
            [
                'url' => $redirectUrl,
                'status' => 200,
            ],
        ];

        $this->browsershot->expects('redirectHistory')
            ->andReturn($redirects);

        $this->crawler->shouldReceive('startCrawling')
            ->with($url);

        $redirectPage = CrawledPage::from([
            'url' => $redirectUrl,
            'responseCode' => 200,
        ]);

        $crawlerService = $this->partialMock(CrawlService::class);
        $crawlerService->expects('singleCrawlUrl')
            ->withArgs(function (SingleCrawlRequest $redirectRequest) use ($redirectUrl) {
                return $redirectUrl === $redirectRequest->websiteUrl;
            })->andReturn($redirectPage);

        $result = $this->crawlService->singleCrawlUrl($request);

        $this->assertEquals($redirectPage, $result);
        $this->assertEquals(301, $result->response_code);
        $this->assertEquals($url, $result->redirect_from);
        $this->assertEquals([$redirectUrl], $result->redirects_to);
    }

    public function test_single_crawl_with_redirect_missing_url(): void
    {
        $url = 'https://hikeseo.co/';
        $redirectUrl = 'https://hikeseo.co/home';
        $request = new SingleCrawlRequest($url, performance: false);

        $this->setupSingleCrawl($request);
        $this->withoutPerformance();

        $this->simpleObserver->expects('getCrawlData')
            ->andReturn(null);

        $redirects = [
            [
                'url' => $url,
                'status' => 301,
            ],
            [
                'url' => null,
                'status' => 200,
            ],
        ];

        $this->browsershot->expects('redirectHistory')
            ->andReturn($redirects);

        $this->crawler->shouldReceive('startCrawling')
            ->with($url);

        $this->expectExceptionMessage('Failed to determine last redirect url');

        $this->crawlService->singleCrawlUrl($request);
    }

    public function test_single_crawl_with_performance(): void
    {
        // TODO https://hikeseo.atlassian.net/browse/CRAW-218
        $this->markTestIncomplete();
    }

    public function test_single_crawl_failed(): void
    {
        $url = 'https://hikeseo.co/';
        $request = new SingleCrawlRequest($url, performance: false);

        $this->setupSingleCrawl($request);
        $this->withoutPerformance();

        $this->simpleObserver->expects('getCrawlData')
            ->andReturn(null);

        $this->browsershot->expects('redirectHistory')
            ->andReturn(null);

        $this->crawler->shouldReceive('startCrawling')
            ->with($url);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to get crawl data for https://hikeseo.co/');

        $this->crawlService->singleCrawlUrl($request);
    }

    public function test_get_pdf(): void
    {
        $request = PdfRequest::from([
            'website_url' => 'https://hikeseo.co/',
            'wait_until' => 'domcontentloaded',
        ]);

        $expectedResult = Str::random();

        $this->browsershot->expects('setUrl')
            ->with($request->websiteUrl)
            ->andReturnSelf();

        $this->browsershot->expects('setOption')
            ->with('waitUntil', $request->waitUntil)
            ->andReturnSelf();

        $this->browsershot->expects('format')
            ->with('A4')
            ->andReturnSelf();

        $this->browsershot->expects('pdf')
            ->andReturn($expectedResult);

        $result = $this->crawlService->getPdf($request);

        $this->assertEquals($expectedResult, $result);
    }

    public function test_screenshot(): void
    {
        $disk = config('capture.storage.disk');
        Storage::fake($disk);

        config(['capture.storage.screenshot_path' => 'screenshots']);

        $request = ScreenshotRequest::from([
            'website_url' => 'https://hikeseo.co/',
            'wait_until' => 'domcontentloaded',
        ]);

        $expectedResult = Str::random();

        $this->browsershot->expects('setUrl')
            ->with($request->websiteUrl)
            ->andReturnSelf();

        $this->browsershot->expects('setOption')
            ->with('waitUntil', $request->waitUntil)
            ->andReturnSelf();

        $this->browsershot->expects('windowSize')
            ->andReturnSelf();

        $this->browsershot->expects('screenshot')
            ->andReturn($expectedResult);

        $result = $this->crawlService->getScreenshot($request);

        $this->assertInstanceOf(ScreenshotResult::class, $result);

        $filePath = rtrim($result->path, '/').'/'.$result->filename;

        Storage::disk($disk)->assertExists($filePath);
    }
}
