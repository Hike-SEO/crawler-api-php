<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Requests\SingleCrawlRequest;
use App\Observers\SimpleCrawlObserver;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;

class CrawlService
{
    public function __construct(
        private Crawler $crawler,
        private readonly SimpleCrawlObserver $crawlObserver,
        private readonly CrawlDataFactory $crawlDataFactory,
    ) {}

    public function singleCrawlUrl(SingleCrawlRequest $request): CrawledPage
    {
        $browsershot = $this->crawler->getBrowsershot();

        $browsershot->setUrl($request->websiteUrl);
        $browsershot->setOption('waitUntil', $request->waitUntil);

        $this->crawler
            ->setCrawlObserver($this->crawlObserver)
            ->setCrawlQueue(new ArrayCrawlQueue)
            ->setTotalCrawlLimit(1)
            ->startCrawling($request->websiteUrl);

        $crawledPage = $this->crawlObserver->getCrawlData();

        $redirects = collect($browsershot->redirectHistory() ?? []);

        // Check if we were redirected
        if ($redirects->count() > 1) {
            $firstRedirect = $redirects->first();
            $lastRedirect = $redirects->last();

            /** @var string|null $lastRedirectUrl */
            $lastRedirectUrl = data_get($lastRedirect, 'url');
            if (! $lastRedirectUrl) {
                throw new \Exception('Failed to determine last redirect url');
            }

            // Crawl the redirected URL instead
            $redirectRequest = clone $request;
            $redirectRequest->websiteUrl = $lastRedirectUrl;
            $crawledPage = $this->singleCrawlUrl($redirectRequest);

            /** @var int $responseCode */
            $responseCode = data_get($firstRedirect, 'status', $crawledPage->response_code);
            /** @var string $redirectFrom */
            $redirectFrom = data_get($firstRedirect, 'url', '');
            /** @var string[] $redirectToUrls */
            $redirectToUrls = collect($redirects)->skip(1)->pluck('url')->all();

            // Update crawled page to include redirect data
            $crawledPage->response_code = $responseCode;
            $crawledPage->redirect_from = $redirectFrom;
            $crawledPage->redirects_to = $redirectToUrls;
        }

        if (! $crawledPage) {
            throw new \Exception("Failed to get crawl data for {$request->websiteUrl}");
        }

        if ($request->performance) {
            $performanceData = $browsershot->evaluate('JSON.stringify(window.performance.getEntries())');
            /** @var array<string, mixed> $performanceData */
            $performanceData = $performanceData ? json_decode($performanceData, true) : [];

            $crawledPage = $this->crawlDataFactory->parsePerformance($crawledPage, $performanceData);
        }

        return $crawledPage;
    }
}
