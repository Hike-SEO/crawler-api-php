<?php

namespace Tests\Unit\Jobs;

use App\Data\CrawledPage;
use App\Http\Requests\FullCrawlRequest;
use App\Http\Requests\SingleCrawlRequest;
use App\Jobs\CrawlPageJob;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use App\Services\CrawlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\InteractsWithTime;
use Tests\TestCase;

class CrawlPageJobTest extends TestCase
{
    use InteractsWithTime;
    use RefreshDatabase;

    protected FullCrawl $fullCrawl;

    protected FullCrawlRequest $fullCrawlRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->fullCrawl = FullCrawl::factory()->create();
        $this->fullCrawlRequest = new FullCrawlRequest($this->fullCrawl->website_id);
    }

    public function test_if_url_is_not_crawlable(): void
    {
        $url = 'https://example.com/report.pdf';

        $this->expectExceptionMessage('Cannot crawl https://example.com/report.pdf');

        CrawlPageJob::dispatchSync(
            $url,
            $this->fullCrawl,
            $this->fullCrawlRequest
        );
    }

    public function test_it_skips_if_at_crawl_limit(): void
    {
        $url = 'https://example.com';

        $this->fullCrawlRequest->crawlLimit = 2;

        PageCrawl::factory()
            ->count(2)
            ->create([
                'full_crawl_id' => $this->fullCrawl->id,
            ]);

        $this->mock(CrawlService::class)
            ->shouldNotReceive('singleCrawlUrl');

        CrawlPageJob::dispatchSync($url, $this->fullCrawl, $this->fullCrawlRequest);

        $this->assertCount(2, $this->fullCrawl->pageCrawls);
    }

    public function test_it_crawls_page_and_internal_links(): void
    {
        $this->freezeSecond();
        Bus::fake();

        $url = 'https://example.com';

        $crawledPage = CrawledPage::fake([
            'url' => $url,
            'internal_links' => [
                [
                    'url' => $url.'/about',
                    'anchor' => [
                        'nodeName' => 'a',
                        'content' => 'About Us',
                    ],
                ],
                [
                    'url' => $url.'/blog',
                    'anchor' => [
                        'nodeName' => 'a',
                        'content' => 'Blog',
                    ],
                ],
            ],
        ]);

        $this->mock(CrawlService::class)
            ->expects('singleCrawlUrl')
            ->withArgs(function (SingleCrawlRequest $requestArg) use ($url) {
                $this->assertEquals($url, $requestArg->websiteUrl);
                $this->assertEquals($this->fullCrawlRequest->waitUntil, $requestArg->waitUntil);
                $this->assertEquals($this->fullCrawlRequest->performance, $requestArg->performance);

                return true;
            })
            ->andReturn($crawledPage);

        (new CrawlPageJob($url, $this->fullCrawl, $this->fullCrawlRequest))
            ->handle(app(CrawlService::class));

        $expectedUrls = [
            'https://example.com/about',
            'https://example.com/blog',
        ];

        Bus::assertDispatched(CrawlPageJob::class, function (CrawlPageJob $job) use ($expectedUrls) {
            $this->assertContains($job->url, $expectedUrls);

            return true;
        });

        $pageCrawl = $this->fullCrawl->pageCrawls()->first();
        $this->assertEquals($pageCrawl->data->toArray(), $crawledPage->toArray());
        $this->assertEquals($pageCrawl->crawled_at, now());
    }

    public function test_handles_failed(): void
    {
        $this->freezeSecond();

        $url = 'https://example.com';

        $exception = new \Exception('404 page not found');

        (new CrawlPageJob($url, $this->fullCrawl, $this->fullCrawlRequest))
            ->failed($exception);

        $pageCrawl = $this->fullCrawl->pageCrawls()->first();

        $this->assertNull($pageCrawl->data);
        $this->assertEquals($pageCrawl->error, $exception->getMessage());
        $this->assertEquals($pageCrawl->crawled_at, now());
    }
}
