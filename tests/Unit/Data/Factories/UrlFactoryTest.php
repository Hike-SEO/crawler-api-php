<?php

namespace Tests\Unit\Data\Factories;

use App\Data\Factories\UrlFactory;
use Tests\TestCase;

class UrlFactoryTest extends TestCase
{
    /**
     * @dataProvider urlProvider
     *
     * @param array{
     *     fullUrl: string,
     *     scheme: string,
     *     host: string,
     *     query: ?string,
     *     path: ?string,
     *     fragment: ?string
     * } $expected
     */
    public function test_it_can_create_a_url_from_a_string(string $url, array $expected): void
    {
        $urlFactory = new UrlFactory;

        $this->assertEquals($expected, $urlFactory->fromString($url)->toArray());
    }

    /**
     * @return array<int, array<int, string|array{fullUrl: string, scheme: string, host: string, query: ?string, path: ?string, fragment: ?string}>>
     */
    public static function urlProvider(): array
    {
        return [
            [
                'https://addsTrailingSlash.com',
                [
                    'fullUrl' => 'https://addstrailingslash.com/',
                    'scheme' => 'https',
                    'host' => 'addstrailingslash.com',
                    'path' => '/',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            [
                'https://with-path.com/some/path',
                [
                    'fullUrl' => 'https://with-path.com/some/path/',
                    'scheme' => 'https',
                    'host' => 'with-path.com',
                    'path' => '/some/path/',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
            [
                'https://with-query.com?query=value',
                [
                    'fullUrl' => 'https://with-query.com/?query=value',
                    'scheme' => 'https',
                    'host' => 'with-query.com',
                    'query' => '?query=value',
                    'path' => '/',
                    'fragment' => null,
                ],
            ],
            [
                'https://with-path-and-query/some-path?query=value',
                [
                    'fullUrl' => 'https://with-path-and-query/some-path/?query=value',
                    'scheme' => 'https',
                    'host' => 'with-path-and-query',
                    'query' => '?query=value',
                    'path' => '/some-path/',
                    'fragment' => null,
                ],
            ],
            [
                'https://with-fragment.com#fragment',
                [
                    'fullUrl' => 'https://with-fragment.com/#fragment',
                    'scheme' => 'https',
                    'host' => 'with-fragment.com',
                    'path' => '/',
                    'query' => null,
                    'fragment' => '#fragment',
                ],
            ],
            [
                'https://with-path-and-fragment/some-path#fragment',
                [
                    'fullUrl' => 'https://with-path-and-fragment/some-path/#fragment',
                    'scheme' => 'https',
                    'host' => 'with-path-and-fragment',
                    'path' => '/some-path/',
                    'query' => null,
                    'fragment' => '#fragment',
                ],
            ],
            [
                'https://with-path-query-and-fragment/some-path?query=value#fragment',
                [
                    'fullUrl' => 'https://with-path-query-and-fragment/some-path/?query=value#fragment',
                    'scheme' => 'https',
                    'host' => 'with-path-query-and-fragment',
                    'path' => '/some-path/',
                    'query' => '?query=value',
                    'fragment' => '#fragment',
                ],
            ],
            [
                'http://http-scheme.com',
                [
                    'fullUrl' => 'http://http-scheme.com/',
                    'scheme' => 'http',
                    'host' => 'http-scheme.com',
                    'path' => '/',
                    'query' => null,
                    'fragment' => null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidSchemeProvider
     */
    public function test_it_throws_an_exception_when_creating_a_url_from_an_invalid_scheme(string $scheme): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid scheme: {$scheme}");

        $urlFactory = new UrlFactory;

        $urlFactory->fromString($scheme.'://example.com');
    }

    /** @return array<int, array<int, string>> */
    public static function invalidSchemeProvider(): array
    {
        return [
            [
                'notascheme',
            ],
            [
                'file',
            ],
            [
                'ftp',
            ],
        ];
    }

    public function test_it_throws_and_exception_when_creating_a_url_from_an_invalid_url(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL: notaurl');

        $urlFactory = new UrlFactory;

        $urlFactory->fromString('notaurl');
    }

    public function test_it_throws_and_exception_when_creating_a_url_from_an_invalid_host(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid URL: https://');

        $urlFactory = new UrlFactory;

        $urlFactory->fromString('https://');
    }
}
