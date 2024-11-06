<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Http\Requests\Websites\StoreRequest;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteServiceTest extends TestCase
{
    use RefreshDatabase;

    public WebsiteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(WebsiteService::class);
    }

    public function test_create_website(): void
    {
        $request = StoreRequest::fake();

        $result = $this->service->createWebsite($request);

        $this->assertInstanceOf(Website::class, $result);
        $this->assertDatabaseHas(Website::class, [
            ...$request->toArray(),
        ]);
    }
}
