<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use App\Services\RobotsService;
use Tests\TestCase;

class RobotsControllerTest extends TestCase
{
    public function test_will_return_robots_txt_for_website(): void
    {
        $expectedResponse = "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php";

        $this->mock(RobotsService::class)
            ->expects('getRobotsTxt')
            ->with('https://example.com')
            ->andReturn($expectedResponse);

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com',
            ])
            ->assertOk()
            ->assertSee($expectedResponse);
    }

    public function test_website_url_is_required(): void
    {
        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'website_url' => [
                    'The website url field is required.',
                ],
            ]);
    }
}
