<?php

namespace App\Services;

use App\Data\Sitemap\SitemapData;
use Exception;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class SitemapService
{
    private const SITEMAP_NAMESPACE = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    private SitemapData $sitemapData;

    public function __construct()
    {
        $this->sitemapData = new SitemapData(
            indices: [],
            urls: []
        );
    }

    public function fetch(string $url): SitemapData
    {
        $this->crawl($url);

        return $this->sitemapData;
    }

    protected function crawl(string $url): void
    {
        try {
            $response = Http::get($url)->throw()->body();
            $this->parse($response);
        } catch (Exception $e) {
            // TODO: Add exception capture
            return;
        }
    }

    protected function parse(string $response, ?string $indices = null): void
    {
        try {
            // Remove any XML processing instructions and comments
            $response = preg_replace('/<\?[^>]*\?>/', '', $response);
            $response = preg_replace('/<!--.*?-->/s', '', $response);

            $xml = new SimpleXMLElement($response);

            $namespaces = $xml->getNamespaces(true);
            $defaultNs = $namespaces[''] ?? self::SITEMAP_NAMESPACE;
            $xml->registerXPathNamespace('sm', $defaultNs);

            // First try to parse as sitemap index
            if ($this->parseSitemapIndex($xml, $indices)) {
                return;
            }

            // If not a sitemap index, try to parse as URL set
            $this->parseUrlset($xml, $indices);

        } catch (Exception $e) {
            // TODO: Add exception capture

            return;
        }
    }

    protected function parseSitemapIndex(SimpleXMLElement $xml, ?string $indices): bool
    {
        $sitemaps = $xml->xpath('//sm:sitemapindex/sm:sitemap/sm:loc');

        if (empty($sitemaps)) {
            return false;
        }

        foreach ($sitemaps as $sitemap) {
            $url = (string) $sitemap;
            if (! empty($url)) {
                try {
                    $sitemapResponse = Http::get($url)->throw()->body();
                    $this->parse($sitemapResponse, $url);
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        return true;
    }

    protected function parseUrlset(SimpleXMLElement $xml, ?string $indices): void
    {
        // Different XPath patterns for URLs
        $urlPatterns = [
            '//sm:urlset/sm:url/sm:loc',  // Standard sitemap
            '//urlset/url/loc',           // No namespace
            '//url/loc',                  // Simple structure
            '//sm:loc',                   // Direct loc elements
            '//loc',                      // Direct loc without namespace
        ];

        foreach ($urlPatterns as $pattern) {
            $urls = $xml->xpath($pattern);
            if (! empty($urls)) {
                foreach ($urls as $urlNode) {
                    $url = (string) $urlNode;
                    if (! empty($url)) {
                        $this->setUrl($url, $indices);
                    }
                }

                return;
            }
        }
    }

    protected function setUrl(string $url, ?string $indices = null): void
    {
        $url = trim($url);

        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        if ($indices) {
            if (! isset($this->sitemapData->indices[$indices])) {
                $this->sitemapData->indices[$indices] = [];
            }
            $this->sitemapData->indices[$indices][] = $url;
        }

        if (! in_array($url, $this->sitemapData->urls)) {
            $this->sitemapData->urls[] = $url;
        }
    }
}
