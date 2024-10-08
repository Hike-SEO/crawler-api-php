<?php

namespace App\Services;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Requests\SingleCrawlRequest;
use App\Observers\SimpleCrawlObserver;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;

class CrawlService
{
    public function __construct(
        private readonly SimpleCrawlObserver $crawlObserver,
        private readonly Browsershot $browsershot,
        private readonly CrawlDataFactory $crawlDataFactory,
    ) {
        $this->browsershot->noSandbox();
    }

    protected function initCrawler(): Crawler
    {
        return Crawler::create()
            ->executeJavaScript()
            ->setBrowsershot($this->browsershot)
            ->setCrawlObserver($this->crawlObserver);
    }

    public function singleCrawlUrl(SingleCrawlRequest $request): CrawledPage
    {
        $this->initCrawler()
            ->setCrawlQueue(new ArrayCrawlQueue)
            ->setTotalCrawlLimit(1)
            ->startCrawling($request->websiteUrl);

        if ($request->performance) {
            $performanceData = $this->browsershot->evaluate('JSON.stringify(window.performance.getEntries())');
            /** @var array<string, mixed> $performanceData */
            $performanceData = $performanceData ? json_decode($performanceData, true) : [];
        }

        $crawlData = $this->crawlObserver->getCrawlData();

        if (! $crawlData) {
            throw new \Exception("Failed to get crawl data for {$request->websiteUrl}");
        }

        if (isset($performanceData)) {
            $crawlData = $this->crawlDataFactory->parsePerformance($crawlData, $performanceData);
        }

        return $crawlData;
    }
}
