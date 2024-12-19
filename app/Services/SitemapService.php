<?php

namespace App\Services;

use App\Data\Sitemap\SitemapData;
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

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
            $parser = new SitemapParser;
            dd($parser);
            $parser->parseRecursive($url);

            dd($parser->getSitemaps());

            // Get all URLs
            foreach ($parser->getURLs() as $url => $tags) {
                $this->sitemapData->urls[] = $url;
            }

            // Get sitemaps and their URLs
            foreach ($parser->getSitemaps() as $sitemapUrl => $tags) {
                $subParser = new SitemapParser;
                $subParser->parse($sitemapUrl);

                $this->sitemapData->indices[$sitemapUrl] = array_keys($subParser->getURLs());
            }

            dd($this->sitemapData);

        } catch (SitemapParserException $e) {
            // Handle parsing errors if needed
        }

        return $this->sitemapData;
    }
}
