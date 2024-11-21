<?php

namespace Tests\Unit\Jobs;

use App\Http\Requests\FullCrawlRequest;
use App\Jobs\CrawlPageJob;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use App\Services\CrawlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrawlPageJobTest extends TestCase
{
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
        $url = 'https://example.com/';

        $this->fullCrawlRequest->crawlLimit = 2;

        PageCrawl::factory()
            ->count(2)
            ->create([
                'full_crawl_id' => $this->fullCrawl->id,
            ]);

        $this->mock(CrawlService::class)
            ->shouldNotReceive('singleCrawlUrl');

        $this->assertCount(2, PageCrawl::all());
    }

    public function test_it_crawls_page_and_internal_links(): void
    {
        // TODO
    }
}
