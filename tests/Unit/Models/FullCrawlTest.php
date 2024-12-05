<?php

namespace Tests\Unit\Models;

use App\Models\FullCrawl;
use App\Models\PageCrawl;
use App\Models\Website;
use Tests\TestCase;

class FullCrawlTest extends TestCase
{
    public function test_has_website(): void
    {
        $website = Website::factory()->create();
        $crawl = FullCrawl::factory()->create([
            'website_id' => $website->id,
        ]);

        $this->assertTrue($website->is($crawl->website));
    }

    public function test_has_page_crawls(): void
    {
        $crawl = FullCrawl::factory()->create();

        $pageCrawls = PageCrawl::factory()
            ->count(5)
            ->create([
                'full_crawl_id' => $crawl->id,
            ]);

        $this->assertCount(5, $crawl->pageCrawls);
        $this->assertEquals(
            $crawl->pageCrawls->pluck('id')->toArray(),
            $pageCrawls->pluck('id')->toArray()
        );
    }
}
