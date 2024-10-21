<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RobotsControllerTest extends TestCase
{
    public function test_will_return_robots_txt_for_website(): void
    {
        Http::fake([
            'https://example.com/robots.txt' => Http::response("User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php", 200),
        ]);

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com',
            ])
            ->assertOk()
            ->assertJson([
                "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php",
            ]);

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com/page.html',
            ])
            ->assertOk()
            ->assertJson([
                "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php",
            ]);
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

    public function test_will_fail_if_timeout_is_longer_than_2_seconds(): void
    {
        Http::fake([
            'https://example.com/robots.txt' => Http::response(null, Response::HTTP_REQUEST_TIMEOUT),
        ]);

        Http::shouldReceive('timeout')
            ->withArgs(['3'])
            ->once();

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com',
            ])
            ->assertServerError();
    }

    public function test_will_handle_redirect(): void
    {
        Http::fake([
            'https://example.com/robots.txt' => Http::response(null, 302, ['Location' => 'https://example.com/other-location']),
            'https://example.com/other-location' => Http::response("User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php"),
        ]);

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com',
            ])
            ->assertOk()
            ->assertJson([
                "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php",
            ]);
    }

    public function test_will_fail_if_too_many_redirects(): void
    {
        Http::fake([
            'https://example.com/robots.txt' => Http::response(null, 302, ['Location' => 'https://example.com/redirect1']),
            'https://example.com/redirect1' => Http::response(null, 302, ['Location' => 'https://example.com/redirect2']),
            'https://example.com/redirect2' => Http::response(null, 302, ['Location' => 'https://example.com/redirect3']),
        ]);

        $this->usingTestApiToken()
            ->postJson('/api/crawl/robots', [
                'website_url' => 'https://example.com',
            ])
            ->assertServerError();
    }
}
