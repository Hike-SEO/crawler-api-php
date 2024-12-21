<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Data\ScreenshotResult;
use App\Http\Requests\ScreenshotRequest;
use App\Services\CrawlService;
use Tests\TestCase;

class ScreenshotControllerTest extends TestCase
{
    public function test_gets_screenshot(): void
    {
        $requestBody = [
            'website_url' => 'https://hikeseo.co/',
            'wait_until' => 'domcontentloaded',
        ];

        $expectedResult = ScreenshotResult::from([
            'path' => 'screesnhots',
            'filename' => 'screenshot.png',
        ]);

        $this->mock(CrawlService::class)
            ->expects('getScreenshot')
            ->withArgs(function (ScreenshotRequest $requestArg) use ($requestBody) {
                $this->assertEquals($requestBody['website_url'], $requestArg->websiteUrl);
                $this->assertEquals($requestBody['wait_until'], $requestArg->waitUntil);

                return true;
            })
            ->andReturn($expectedResult);

        $this->withHeader('x-api-key', config('crawler.api_key'))
            ->postJson(route('api.capture.screenshot'), $requestBody)
            ->assertSuccessful()
            ->assertJson($expectedResult->toArray());
    }
}
