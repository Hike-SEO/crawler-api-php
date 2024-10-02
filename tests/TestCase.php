<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function usingTestApiToken(): void
    {
        config()->set([
            'crawler.api_key' => 'secret-key',
        ]);

        $this->withHeader('x-api-key', 'secret-key');
    }
}
