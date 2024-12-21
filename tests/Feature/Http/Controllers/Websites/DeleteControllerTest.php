<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Websites;

use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_be_authenticated(): void
    {
        $website = Website::factory()->create();

        $this->delete(route('api.websites.delete', [$website]))
            ->assertUnauthorized();
    }

    public function test_it_deletes_website(): void
    {
        $website = Website::factory()->create([
            'url' => 'https://example.com',
        ]);

        $service = $this->mock(WebsiteService::class);
        $service->expects('deleteWebsite')
            ->withArgs(function (Website $websiteArg) use ($website) {
                $this->assertTrue($website->is($websiteArg));

                return true;
            });

        $this
            ->usingTestApiToken()
            ->delete(route('api.websites.delete', [$website]))
            ->assertNoContent();
    }
}
