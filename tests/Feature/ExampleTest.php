<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        // Added for the sake of the workflow
        config()->set([
            'crawler.api_key' => 'secret-key',
        ]);

        $response = $this->withHeader('x-api-key', 'secret-key')->get('/');

        $response->assertStatus(200);
    }
}
