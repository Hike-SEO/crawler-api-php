<?php

namespace App\Services;

use App\Data\CrawlData;
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

    public function singleCrawlUrl(SingleCrawlRequest $request): CrawlData
    {
        $this->initCrawler()
            ->setCrawlQueue(new ArrayCrawlQueue)
            ->setTotalCrawlLimit(1)
            ->startCrawling($request->websiteUrl);

        $performanceData = $this->browsershot->evaluate('JSON.stringify(window.performance.getEntries())');

        $performanceData = $performanceData ? json_decode($performanceData, true) : [];

        $crawlData = $this->crawlObserver->getCrawlData();

        $crawlData = $this->crawlDataFactory->parsePerformance($crawlData, $performanceData);

        return $crawlData;
    }
}
