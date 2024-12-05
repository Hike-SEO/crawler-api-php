<?php

namespace Tests\Unit\Console\Commands;

use App\Jobs\FinishFullCrawlJob;
use App\Models\FullCrawl;
use App\Models\PageCrawl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ProcessFinishCrawlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_dispatches_finished_crawls(): void
    {
        $finishedCrawl = FullCrawl::factory()->create([
            'finished_at' => now(),
        ]);

        $runningCrawl = FullCrawl::factory()->create();
        PageCrawl::factory()->create([
            'full_crawl_id' => $runningCrawl->id,
        ]);

        $finishingCrawl = FullCrawl::factory()->create();
        PageCrawl::factory()->create([
            'full_crawl_id' => $runningCrawl->id,
            'crawled_at' => now()->subHour(),
        ]);

        Bus::fake([
            FinishFullCrawlJob::class,
        ]);

        $this->artisan('app:process-finished-full-crawls');

        Bus::assertDispatched(FinishFullCrawlJob::class, function ($job) use ($finishingCrawl) {
            $this->assertTrue($job->fullCrawl->is($finishingCrawl));

            return true;
        });
    }
}
