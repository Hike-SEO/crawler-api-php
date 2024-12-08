<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Requests\PdfRequest;
use App\Services\CrawlService;
use Illuminate\Support\Str;
use Tests\TestCase;

class PdfControllerTest extends TestCase
{
    public function test_gets_pdf(): void
    {
        $requestBody = [
            'website_url' => 'https://hikeseo.co/',
            'wait_until' => 'domcontentloaded',
        ];

        $pdfString = Str::random();

        $this->mock(CrawlService::class)
            ->expects('getPdf')
            ->withArgs(function (PdfRequest $requestArg) use ($requestBody) {
                $this->assertEquals($requestBody['website_url'], $requestArg->websiteUrl);
                $this->assertEquals($requestBody['wait_until'], $requestArg->waitUntil);

                return true;
            })
            ->andReturn($pdfString);

        $this->withHeader('x-api-key', config('crawler.api_key'))
            ->postJson(route('api.capture.pdf'), $requestBody)
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertHeader('Content-Disposition', 'inline; filename="website.pdf"');
    }
}
