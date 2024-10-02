<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RobotsControllerTest extends TestCase
{
    public function test_will_return_robots_txt_for_website(): void
    {
        $this->usingTestApiToken();

        Http::fake([
            'https://example.com/robots.txt' => Http::response("User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php", 200),
        ]);

        $this->postJson('/api/crawl/robots', [
            'website_url' => 'https://example.com',
        ])
            ->assertOk()
            ->assertJson([
                "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php",
            ]);

        $this->postJson('/api/crawl/robots', [
            'website_url' => 'https://example.com/page.html',
        ])
            ->assertOk()
            ->assertJson([
                "User-agent: *\nDisallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php",
            ]);
    }

    public function test_website_url_is_required(): void
    {
        $this->usingTestApiToken();

        $this->postJson('/api/crawl/robots')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'website_url' => [
                    'The website url field is required.',
                ],
            ]);
    }
}
