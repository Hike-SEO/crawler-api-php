<?php

namespace App\Services;

use App\Data\Sitemap\SitemapData;
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;
use function Sentry\captureException;

class SitemapService
{
    private SitemapData $sitemapData;

    public function __construct()
    {
        $this->sitemapData = new SitemapData(
            indices: [],
            urls: []
        );
    }

    public function parse(string $url): SitemapData
    {
        try {
            $parser = app(SitemapParser::class);
            $parser->parseRecursive($url);

            // Get URLs from the sitemap (needed in case the sitemap is not an index)
            $this->sitemapData->urls = array_keys($parser->getURLs());

            // Get sitemap index and their URLs
            foreach ($parser->getSitemaps() as $sitemapUrl => $tags) {
                $subParser = app(SitemapParser::class);
                $subParser->parse($sitemapUrl);

                $urls = array_keys($subParser->getURLs());

                $this->sitemapData->indices[$sitemapUrl] = $urls;
            }
        } catch (SitemapParserException $e) {
            captureException($e);
        }

        return $this->sitemapData;
    }
}
