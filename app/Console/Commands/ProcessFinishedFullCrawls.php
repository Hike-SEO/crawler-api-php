<?php

namespace App\Console\Commands;

use App\Jobs\FinishFullCrawlJob;
use App\Models\FullCrawl;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class ProcessFinishedFullCrawls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-finished-full-crawls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates full crawls that have finished';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        FullCrawl::query()
            ->whereNull('finished_at')
            ->whereDoesntHave('pageCrawls', function (Builder $query) {
                return $query->whereNull('crawled_at');
            })
            ->cursor()
            ->each(function (FullCrawl $fullCrawl) {
                FinishFullCrawlJob::dispatch($fullCrawl);
            });

        return self::SUCCESS;
    }
}
