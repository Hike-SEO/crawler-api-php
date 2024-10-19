<?php

namespace App\Data\Factories;

use App\Data\Url;
use Illuminate\Support\Str;

class UrlFactory
{
    public function fromString(string $url): Url
    {
        $parsedUrl = parse_url($url);

        if (!is_array($parsedUrl)) {
            throw new \InvalidArgumentException("Invalid URL: {$url}");
        }

        $scheme = Str::lower($parsedUrl['scheme'] ?? 'https');
        if (!in_array($scheme, ['http', 'https'])) {
            throw new \InvalidArgumentException("Invalid scheme: {$scheme}");
        }

        $host = Str::lower($parsedUrl['host'] ?? '');
        if (!$host) {
            throw new \InvalidArgumentException("Invalid URL: {$url}");
        }

        $path = Str::finish(Str::lower($parsedUrl['path'] ?? ''), '/');
        $query = Str::lower(isset($parsedUrl['query']) ? "?{$parsedUrl['query']}" : '');
        $fragment = Str::lower(isset($parsedUrl['fragment']) ? "#{$parsedUrl['fragment']}" : '');

        $fullUrl = "$scheme://$host$path$query$fragment";

        return Url::from([
            'fullUrl' => $fullUrl,
            'scheme' => $scheme,
            'host' => $host,
            'port' => $parsedUrl['port'] ?? null,
            'query' => $query ?: null,
            'path' => $path,
            'fragment' => $fragment ?: null,
        ]);
    }
}
