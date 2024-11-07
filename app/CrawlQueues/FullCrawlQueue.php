<?php

namespace App\CrawlQueues;

use App\Models\FullCrawl;
use App\Models\PageCrawl;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlQueues\CrawlQueue;
use Spatie\Crawler\CrawlUrl;

class FullCrawlQueue implements CrawlQueue
{
    public function __construct(
        public FullCrawl $fullCrawl,
    ) {}

    public function add(CrawlUrl $url): CrawlQueue
    {
        $pageCrawl = PageCrawl::query()
            ->create([
                'url' => $url->url->__toString(),
                'full_crawl_id' => $this->fullCrawl->id,
            ]);

        $url->setId($pageCrawl->id);

        return $this;
    }

    public function has(UriInterface|CrawlUrl $crawlUrl): bool
    {
        return $this->getPageCrawl($crawlUrl) !== null;
    }

    public function hasPendingUrls(): bool
    {
        return PageCrawl::query()
            ->where('full_crawl_id', $this->fullCrawl->id)
            ->whereNull('crawled_at')
            ->exists();
    }

    public function getUrlById($id): CrawlUrl
    {
        $pageCrawl = PageCrawl::query()->findOrFail($id);

        return $this->pageCrawlToCrawlUrl($pageCrawl);
    }

    public function getPendingUrl(): ?CrawlUrl
    {
        $pageCrawl = PageCrawl::query()
            ->where('full_crawl_id', $this->fullCrawl->id)
            ->whereNull('crawled_at')
            ->first();

        if (! $pageCrawl) {
            return null;
        }

        return $this->pageCrawlToCrawlUrl($pageCrawl);
    }

    public function hasAlreadyBeenProcessed(CrawlUrl $url): bool
    {
        return $this->getPageCrawl($url)?->crawled_at !== null;
    }

    public function markAsProcessed(CrawlUrl $crawlUrl): void
    {
        $pageCrawl = $this->getPageCrawl($crawlUrl);
        if (! $pageCrawl) {
            return;
        }

        $pageCrawl->refresh()->update([
            'crawled_at' => now(),
        ]);
    }

    public function getProcessedUrlCount(): int
    {
        return $this->fullCrawl->pageCrawls()
            ->whereNotNull('crawled_at')
            ->count();
    }

    public function getPageCrawl(UriInterface|CrawlUrl $crawlUrl): ?PageCrawl
    {
        $url = ($crawlUrl instanceof CrawlUrl) ? $crawlUrl->url : $crawlUrl;
        /** @var PageCrawl|null $pageCrawl */
        $pageCrawl = PageCrawl::query()
            ->where('full_crawl_id', $this->fullCrawl->id)
            ->where('url', $url->__toString())
            ->first();

        return $pageCrawl;
    }

    protected function pageCrawlToCrawlUrl(PageCrawl $pageCrawl): CrawlUrl
    {
        return CrawlUrl::create(new Uri($pageCrawl->url), id: $pageCrawl->id);
    }
}
