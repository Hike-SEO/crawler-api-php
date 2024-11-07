<?php

declare(strict_types=1);

namespace App\Services;

use App\CrawlQueues\FullCrawlQueue;
use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Requests\FullCrawlRequest;
use App\Http\Requests\SingleCrawlRequest;
use App\Jobs\StartFullCrawlJob;
use App\Models\FullCrawl;
use App\Models\Website;
use App\Observers\FullCrawlObserver;
use App\Observers\SimpleCrawlObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Crawler\CrawlQueues\ArrayCrawlQueue;

class CrawlService
{
    public function __construct(
        private readonly Crawler $crawler,
        private readonly SimpleCrawlObserver $simpleCrawlObserver,
        private readonly FullCrawlObserver $fullCrawlObserver,
        private readonly CrawlDataFactory $crawlDataFactory,
    ) {}

    public function singleCrawlUrl(SingleCrawlRequest $request): CrawledPage
    {
        $browsershot = $this->crawler->getBrowsershot();

        $browsershot->setUrl($request->websiteUrl);
        $browsershot->setOption('waitUntil', $request->waitUntil);

        $this->crawler
            ->setCrawlObserver($this->simpleCrawlObserver)
            ->setCrawlQueue(new ArrayCrawlQueue)
            ->setTotalCrawlLimit(1)
            ->startCrawling($request->websiteUrl);

        $crawledPage = $this->simpleCrawlObserver->getCrawlData();

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
            $crawledPage = app(self::class)->singleCrawlUrl($redirectRequest);

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
            $performanceDataJson = $browsershot->evaluate('JSON.stringify(window.performance.getEntries())');
            /** @var array<string, mixed> $performanceData */
            $performanceData = $performanceDataJson ? json_decode($performanceDataJson, true) : [];

            $crawledPage = $this->crawlDataFactory->parsePerformance($crawledPage, $performanceData);
        }

        return $crawledPage;
    }

    public function startFullCrawl(Website $website, FullCrawlRequest $request): FullCrawl
    {
        $fullCrawl = FullCrawl::query()
            ->create([
                'website_id' => $website->id,
            ]);

        StartFullCrawlJob::dispatch($fullCrawl, $request);

        return $fullCrawl;
    }

    public function runFullCrawl(FullCrawl $fullCrawl, FullCrawlRequest $request): FullCrawl
    {
        $browsershot = $this->crawler->getBrowsershot();
        $url = $fullCrawl->website->url;
        $this->fullCrawlObserver->setFullCrawl($fullCrawl);

        $browsershot->setUrl($url);
        $browsershot->setOption('waitUntil', $request->waitUntil ?? $website->wait_until);

        $this->crawler
            ->setCrawlObserver($this->fullCrawlObserver)
            ->setCrawlProfile(new CrawlInternalUrls($url))
            ->setCrawlQueue(new FullCrawlQueue($fullCrawl))
            ->setMaximumDepth(2)
            ->setTotalCrawlLimit(10)
            ->setDelayBetweenRequests(2500)
            ->startCrawling($url);

        return $fullCrawl;
    }
}
