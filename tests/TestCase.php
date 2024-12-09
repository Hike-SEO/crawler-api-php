<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setApiKey(string $apiKey): void
    {
        config()->set([
            'crawler.api_key' => $apiKey,
        ]);
    }
}
