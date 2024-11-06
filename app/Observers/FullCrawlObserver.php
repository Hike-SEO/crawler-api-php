<?php

namespace App\Observers;

use App\Data\Factories\CrawlDataFactory;
use App\Models\FullCrawl;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver as BaseCrawlObserver;

class FullCrawlObserver extends BaseCrawlObserver
{
    private FullCrawl $fullCrawl;

    public function __construct(
        public readonly CrawlDataFactory $crawlDataFactory,
    ) {}

    public function setFullCrawl(FullCrawl $fullCrawl): void
    {
        $this->fullCrawl = $fullCrawl;
    }

    /*
     * Called when the crawler has crawled the given url successfully.
     */
    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null,
        ?string $linkText = null,
    ): void {
        $this->updateCrawledPage($url, $response);
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
        $response = $requestException->getResponse();
        if (! $response) {
            throw $requestException;
        }

        $this->updateCrawledPage($url, $response);
    }

    /**
     * Called when the crawl has ended.
     */
    public function finishedCrawling(): void
    {
        $this->fullCrawl->update([
            'finished_at' => now(),
        ]);
    }

    public function updateCrawledPage(UriInterface $url, ResponseInterface $response): void
    {
        $crawlData = $this->crawlDataFactory->fromResponse($url, $response);

        $pageCrawl = $this->fullCrawl
            ->pageCrawls()
            ->where('url', $url->__toString())
            ->first();

        $pageCrawl?->update([
            'data' => $crawlData,
            'finished_at' => now(),
        ]);
    }
}
