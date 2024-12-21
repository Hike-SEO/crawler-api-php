<?php

namespace Tests\Feature\Http\Controllers\Websites;

use App\Http\Middleware\AuthenticateSecretKey;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_be_authenticated(): void
    {
        $this->getJson(route('api.websites.index'))
            ->assertUnauthorized();
    }

    public function test_returns_websites(): void
    {
        Website::factory()
            ->count(3)
            ->create();

        $this
            ->withoutMiddleware([AuthenticateSecretKey::class])
            ->getJson(route('api.websites.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'url',
                    ],
                ],
            ]);
    }
}
