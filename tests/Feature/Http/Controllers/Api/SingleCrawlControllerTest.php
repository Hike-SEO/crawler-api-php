<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Data\CrawledPage;
use App\Http\Requests\SingleCrawlRequest;
use App\Services\CrawlService;
use Tests\TestCase;

class SingleCrawlControllerTest extends TestCase
{
    public function test_runs_single_crawl(): void
    {
        $requestBody = [
            'website_url' => 'https://hikeseo.co/',
            'wait_until' => 'domcontentloaded',
            'performance' => true,
        ];

        $crawledPage = CrawledPage::from([
            'url' => 'https://hikeseo.co/',
        ]);

        $this->mock(CrawlService::class)
            ->expects('singleCrawlUrl')
            ->withArgs(function (SingleCrawlRequest $requestArg) use ($requestBody) {
                $this->assertEquals($requestBody['website_url'], $requestArg->websiteUrl);
                $this->assertEquals($requestBody['wait_until'], $requestArg->waitUntil);
                $this->assertEquals($requestBody['performance'], $requestArg->performance);

                return true;
            })
            ->andReturn($crawledPage);

        $this->postJson(route('api.crawl.single'), $requestBody)
            ->assertSuccessful()
            ->assertJson($crawledPage->toArray());
    }
}
