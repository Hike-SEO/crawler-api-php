<?php

namespace Feature\Http\Controllers\Websites;

use App\Http\Middleware\AuthenticateSecretKey;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CreateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_be_authenticated(): void
    {
        $this->postJson(route('api.websites.create'), [])
            ->assertUnauthorized();
    }

    #[DataProvider('validatesInputDataProvider')]
    public function test_validates_input(array $input, array $expectedErrors): void
    {
        Website::factory()->create([
            'url' => 'https://example.com',
        ]);
        $this
            ->withoutMiddleware([AuthenticateSecretKey::class])
            ->postJson(route('api.websites.create'), $input)
            ->assertJsonValidationErrors($expectedErrors);
    }

    public static function validatesInputDataProvider(): array
    {
        return [
            [
                'input' => [],
                'expectedErrors' => [
                    'url' => 'The url field is required.',
                ],
            ],
            [
                'input' => [
                    'url' => 'not a url',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a valid URL.',
                ],
            ],
            [
                'input' => [
                    'url' => 'https://example.com',
                ],
                'expectedErrors' => [
                    'url' => 'The url has already been taken.',
                ],
            ],
            // TODO add more
        ];
    }
}
