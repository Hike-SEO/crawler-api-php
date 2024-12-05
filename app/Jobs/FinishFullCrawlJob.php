<?php

namespace App\Jobs;

use App\Models\FullCrawl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FinishFullCrawlJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public FullCrawl $fullCrawl) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->fullCrawl->update([
            'finished_at' => now(),
        ]);

        $pagesData = $this->fullCrawl->pageCrawls
            ->whereNotNull('data')
            ->pluck('data');

        /** @var string $storageDisk */
        $storageDisk = config('crawl.storage.disk');
        /** @var string $storagePath */
        $storagePath = config('crawl.storage.path');

        $fileName = "{$this->fullCrawl->id}.json";
        $filePath = rtrim($storagePath, '/').'/'.$fileName;

        if (! Storage::disk($storageDisk)->directoryExists($storagePath)) {
            Storage::disk($storageDisk)->makeDirectory($storagePath);
        }

        $stored = Storage::disk($storageDisk)
            ->put($filePath, $pagesData->toJson());

        if (! $stored) {
            throw new \Exception("Failed to store data for full crawl #{$this->fullCrawl->id}");
        }

        $this->fullCrawl->update([
            'file_path' => $filePath,
        ]);

        $this->fullCrawl->pageCrawls()->whereNull('error')->delete();

        /** @var string|null $platformUrl */
        $platformUrl = config('app.platform_url');
        if (! $platformUrl) {
            return;
        }

        Http::throw()
            ->post("{$platformUrl}/api/crawler/v4/callback/{$this->fullCrawl->website_id}", [
                'pagesFileName' => $filePath,
            ]);
    }

    public function uniqueId(): string
    {
        return "{$this->fullCrawl->id}";
    }
}
