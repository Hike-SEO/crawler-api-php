<?php

namespace Tests\Unit\Jobs;

use App\Jobs\FinishFullCrawlJob;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinishFullCrawlJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_finish_full_crawl(): void
    {
        $this->freezeSecond();

        $disk = config('crawl.storage.disk');
        $platformUrl = 'https://example.com';

        config(['app.platform_url' => $platformUrl]);
        config(['crawl.storage.path' => 'crawl-data']);
        Http::preventStrayRequests();

        $storage = Storage::fake($disk);

        $fullCrawl = FullCrawl::factory()->create();

        $crawledPages = PageCrawl::factory()
            ->count(2)
            ->withData()
            ->create([
                'full_crawl_id' => $fullCrawl->id,
            ]);

        $expectedFilePath = "crawl-data/{$fullCrawl->id}.json";

        $callbackUrl = "{$platformUrl}/api/crawler/v4/callback/{$fullCrawl->website_id}";

        Http::fake([
            $callbackUrl => Http::response(),
        ]);

        FinishFullCrawlJob::dispatchSync($fullCrawl);

        $this->assertEquals(now(), $fullCrawl->fresh()->finished_at);

        $storage->assertExists($expectedFilePath);
        $this->assertEquals($expectedFilePath, $fullCrawl->fresh()->file_path);

        Http::assertSent(function (Request $request) use ($expectedFilePath, $callbackUrl) {
            $body = json_decode($request->body(), false);

            $this->assertEquals($callbackUrl, $request->url());
            $this->assertEquals($expectedFilePath, $body->pagesFileName);

            return true;
        });
    }
}
