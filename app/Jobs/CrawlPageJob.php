<?php

namespace App\Jobs;

use App\Data\CrawledPageLink;
use App\Http\Requests\FullCrawlRequest;
use App\Http\Requests\SingleCrawlRequest;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use App\Services\CrawlService;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CrawlPageJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string           $url,
        public FullCrawl        $fullCrawl,
        public FullCrawlRequest $crawlRequest,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(CrawlService $crawlService): void
    {
        if (!$this->isUrlCrawlable()) {
            throw new \Exception("Cannot crawl {$this->url}");
        }

        if (
            $this->crawlRequest->crawlLimit
            && $this->fullCrawl->pageCrawls()->count() >= $this->crawlRequest->crawlLimit
        ) {
            return;
        }

        $pageCrawl = $this->getPageCrawl();

        $singleCrawlRequest = new SingleCrawlRequest(
            websiteUrl: $this->url,
            waitUntil: $this->crawlRequest->waitUntil,
            performance: $this->crawlRequest->performance,
        );

        $crawledPageData = $crawlService->singleCrawlUrl($singleCrawlRequest);
        $pageCrawl->update([
            'data' => $crawledPageData,
            'crawled_at' => now(),
        ]);

        $crawledPageData->internal_links
            ->toCollection()
            ->collect()
            ->each(function (CrawledPageLink $internalLink) {
                CrawlPageJob::dispatch($internalLink->url, $this->fullCrawl, $this->crawlRequest);
            });
    }

    public function uniqueId(): string
    {
        return $this->url;
    }

    public function fail($exception = null)
    {
        $this->getPageCrawl()
            ->update([
                'error' => $exception->getMessage(),
                'crawled_at' => now(),
            ]);
    }

    protected function getPageCrawl(): PageCrawl
    {
        /** @var PageCrawl $pageCrawl */
        $pageCrawl = $this->fullCrawl->pageCrawls()
            ->firstOrCreate([
                'url' => $this->url,
            ]);

        return $pageCrawl;
    }

    protected function isUrlCrawlable(): bool
    {
        $url = new Uri($this->url);

        if (!Str::contains($url->getPath(), '.')) {
            return true;
        }

        $type = Str::of($url->getPath())->afterLast('.');
        return in_array($type, [
            '',
            '/',
            'html',
            'htm',
            'shtml',
            'xhtml',
            'php',
            'asp',
            'aspx',
            'jsp',
        ]);
    }
}
