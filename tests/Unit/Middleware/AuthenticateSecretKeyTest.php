<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AuthenticateSecretKey;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthenticateSecretKeyTest extends TestCase
{
    public function test_authenticate_secret_key_returns_unauthenticated_without_key(): void
    {
        config()->set([
            'crawler.api_key' => 'secret-key',
        ]);

        $request = new Request;

        $response = $this->app->make(AuthenticateSecretKey::class)
            ->handle($request, fn (): Response => response(''));

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    public function test_authenticate_secret_key(): void
    {
        config()->set([
            'crawler.api_key' => 'secret-key',
        ]);

        $request = new Request;

        $request->headers->set('x-api-key', 'secret-key');

        $response = $this->app->make(AuthenticateSecretKey::class)
            ->handle($request, fn (): Response => response(''));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
