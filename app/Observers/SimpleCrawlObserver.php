<?php

namespace App\Observers;

use App\Data\CrawledPage;
use App\Data\Factories\CrawlDataFactory;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver as BaseCrawlObserver;

class SimpleCrawlObserver extends BaseCrawlObserver
{
    private ?CrawledPage $crawlData = null;

    public function __construct(
        public readonly CrawlDataFactory $crawlDataFactory,
    ) {}

    /*
     * Called when the crawler has crawled the given url successfully.
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {
        $this->crawlData = $this->crawlDataFactory->fromResponse($url, $response);
    }

    /*
     * Called when the crawler had a problem crawling the given url.
     */
    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {
        throw $requestException;
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        //
    }

    public function getCrawlData(): ?CrawledPage
    {
        return $this->crawlData;
    }
}
