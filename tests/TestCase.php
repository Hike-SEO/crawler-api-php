<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function usingTestApiToken(): static
    {
        config()->set([
            'crawler.api_key' => 'secret-key',
        ]);

        return $this->withHeader('x-api-key', 'secret-key');
    }
}
