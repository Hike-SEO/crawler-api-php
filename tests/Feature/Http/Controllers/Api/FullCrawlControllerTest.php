<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Requests\FullCrawlRequest;
use App\Models\FullCrawl;
use App\Models\Website;
use App\Services\CrawlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullCrawlControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_full_crawl(): void
    {
        $website = Website::factory()->create();

        $requestBody = [
            'website_id' => $website->id,
            'crawl_limit' => 200,
            'wait_until' => 'domcontentloaded',
            'performance' => true,
        ];

        $fullCrawl = FullCrawl::factory()->create([
            'website_id' => $website->id,
        ]);

        $this->mock(CrawlService::class)
            ->expects('startFullCrawl')
            ->withArgs(function (Website $websiteArg, FullCrawlRequest $requestArg) use ($website, $requestBody) {
                $this->assertTrue($websiteArg->is($website));

                $this->assertEquals($requestBody['crawl_limit'], $requestArg->crawlLimit);
                $this->assertEquals($requestBody['wait_until'], $requestArg->waitUntil);
                $this->assertEquals($requestBody['performance'], $requestArg->performance);

                return true;
            })
            ->andReturn($fullCrawl);

        $this->withHeader('x-api-key', config('crawler.api_key'))
            ->postJson(route('api.crawl.full'), $requestBody)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'website',
                ],
            ])
            ->assertJsonPath('data.id', $fullCrawl->id);
    }

    public function test_returns_running_full_crawl(): void
    {
        // TODO
        $this->markTestIncomplete();
    }
}
