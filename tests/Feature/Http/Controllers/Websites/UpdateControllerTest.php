<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Websites;

use App\Enums\WaitUntil;
use App\Http\Requests\Websites\StoreRequest;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_be_authenticated(): void
    {
        $website = Website::factory()->create();

        $this->putJson(route('api.websites.update', [$website]), [])
            ->assertUnauthorized();
    }

    #[DataProvider('validatesInputDataProvider')]
    public function test_validates_input(array $input, array $expectedErrors): void
    {
        $website = Website::factory()->create([
            'url' => 'https://demo.com',
        ]);

        $otherWebsite = Website::factory()->create([
            'url' => 'https://example.com',
        ]);
        $this
            ->usingTestApiToken()
            ->putJson(route('api.websites.update', [$website]), $input)
            ->assertUnprocessable()
            ->assertJsonValidationErrors($expectedErrors);
    }

    public function test_it_updates_website(): void
    {
        $data = [
            'url' => 'https://example.com',
            'ignore_robots_txt' => true,
            'wait_until' => WaitUntil::DOM_CONTENT_LOADED->value,
            'page_timeout' => 1000,
            'max_concurrent_pages' => 10,
            'hike_user_agent' => true,
        ];

        $website = Website::factory()->create([
            'url' => 'https://example.com',
        ]);

        $service = $this->mock(WebsiteService::class);
        $service->expects('updateWebsite')
            ->withArgs(function (Website $websiteArg, StoreRequest $request) use ($website, $data) {
                $this->assertTrue($website->is($websiteArg));
                $this->assertEquals($data['url'], $request->url);
                $this->assertEquals($data['ignore_robots_txt'], $request->ignoreRobotsTxt);
                $this->assertEquals($data['wait_until'], $request->waitUntil->value);
                $this->assertEquals($data['page_timeout'], $request->pageTimeout);
                $this->assertEquals($data['max_concurrent_pages'], $request->maxConcurrentPages);
                $this->assertEquals($data['hike_user_agent'], $request->hikeUserAgent);

                return true;
            })
            ->andReturnUsing(fn () => $website->fresh());

        $this
            ->usingTestApiToken()
            ->putJson(route('api.websites.update', [$website]), $data)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'url',
                ],
            ]);
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
            [
                'input' => [
                    'wait_until' => 'forever',
                ],
                'expectedErrors' => [
                    'wait_until' => 'The selected wait until is invalid.',
                ],
            ],
            [
                'input' => [
                    'page_timeout' => 200,
                ],
                'expectedErrors' => [
                    'page_timeout' => 'The page timeout field must be between 500 and 30000',
                ],
            ],
            [
                'input' => [
                    'page_timeout' => 999999,
                ],
                'expectedErrors' => [
                    'page_timeout' => 'The page timeout field must be between 500 and 30000',
                ],
            ],
            [
                'input' => [
                    'max_concurrent_pages' => 200,
                ],
                'expectedErrors' => [
                    'max_concurrent_pages' => 'The max concurrent pages field must be between 1 and 100.',
                ],
            ],
            [
                'input' => [
                    'max_concurrent_pages' => 999999,
                ],
                'expectedErrors' => [
                    'max_concurrent_pages' => 'The max concurrent pages field must be between 1 and 100.',
                ],
            ],
        ];
    }
}
