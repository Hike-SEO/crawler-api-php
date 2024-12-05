<?php

namespace Tests\Unit\Models;

use App\Data\CrawledPage;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageCrawlTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_crawl(): void
    {
        $fullCrawl = FullCrawl::factory()->create();

        $pageCrawl = PageCrawl::factory()->create([
            'full_crawl_id' => $fullCrawl->id,
        ]);

        $this->assertTrue($fullCrawl->is($pageCrawl->fullCrawl));
    }

    public function test_data(): void
    {
        $pageCrawl = PageCrawl::factory()->create([
            'data' => CrawledPage::fake(),
        ]);

        $this->assertInstanceOf(CrawledPage::class, $pageCrawl->data);
    }
}
