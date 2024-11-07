<?php

namespace App\Jobs;

use App\Http\Requests\FullCrawlRequest;
use App\Models\FullCrawl;
use App\Services\CrawlService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartFullCrawlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public FullCrawl $fullCrawl,
        public FullCrawlRequest $crawlRequest,
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(CrawlService $crawlService): void
    {
        $crawlService->runFullCrawl($this->fullCrawl, $this->crawlRequest);
    }
}
