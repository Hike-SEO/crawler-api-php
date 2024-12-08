<?php

namespace Tests\Unit\Services;

use App\Services\RobotsService;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RobotsServiceTest extends TestCase
{
    private RobotsService $robotsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->robotsService = app(RobotsService::class);
        Http::preventStrayRequests();
    }

    public function test_makes_http_request(): void
    {
        $expectedResult = "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php";

        Http::fake([
            'https://example.com/robots.txt' => Http::response($expectedResult, 200),
        ]);

        $result = $this->robotsService->getRobotsTxt('https://example.com');

        $this->assertEquals($expectedResult, $result);

        $result = $this->robotsService->getRobotsTxt('https://example.com/index.html');

        $this->assertEquals($expectedResult, $result, 'Handles stripping off path');
    }

    public function test_timeout_is_set(): void
    {
        $pendingRequest = $this->partialMock(PendingRequest::class);

        $pendingRequest->expects('timeout')
            ->withArgs([3])
            ->andReturnSelf();

        $pendingRequest->expects('get')
            ->withArgs(['https://example.com/robots.txt'])
            ->andThrows(new RequestException(
                new HttpResponse(
                    new GuzzleResponse(504)
                ),
            ));

        Http::expects('throw')
            ->andReturn($pendingRequest);

        $this->expectException(RequestException::class);

        $this->robotsService->getRobotsTxt('https://example.com');
    }

    public function test_will_handle_redirect(): void
    {
        $expectedResult = "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php";

        Http::fake([
            'https://example.com/robots.txt' => Http::response(null, 302, ['Location' => 'https://example.com/other-location']),
            'https://example.com/other-location' => Http::response($expectedResult, 200),
        ]);

        $result = $this->robotsService->getRobotsTxt('https://example.com');

        $this->assertEquals($expectedResult, $result);
    }

    public function test_will_fail_if_too_many_redirects(): void
    {
        Http::fake([
            'https://example.com/robots.txt' => Http::response(null, 302, ['Location' => 'https://example.com/redirect1']),
            'https://example.com/redirect1' => Http::response(null, 302, ['Location' => 'https://example.com/redirect2']),
            'https://example.com/redirect2' => Http::response(null, 302, ['Location' => 'https://example.com/redirect3']),
        ]);

        $this->expectException(TooManyRedirectsException::class);

        $result = $this->robotsService->getRobotsTxt('https://example.com');
    }
}
