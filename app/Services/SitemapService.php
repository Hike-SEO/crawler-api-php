<?php

namespace App\Services;

use App\Data\Sitemap\SitemapData;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use XMLReader;

class SitemapService
{
    private SitemapData $sitemapData;

    public function __construct(
    ) {
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
        } catch (\Exception) {
            // TODO: Add exception capture

            return;
        }
    }

    protected function parse(string $response, ?string $indices = null): void
    {
        $reader = XMLReader::XML($response);

        while ($reader->read()) {
            // If sitemap index we need to follow and return the urls in the next sitemap
            $xml = new SimpleXMLElement($reader->readOuterXML());

            // Check if the sitemap link is an indices
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'sitemapindex') {
                foreach ($xml->sitemap as $sitemap) {
                    $url = $sitemap->loc->__toString();

                    $sitemapChildResponse = Http::get($url);

                    $this->parse($sitemapChildResponse->body(), $url);
                }
            }

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'urlset') {
                foreach ($xml as $node) {
                    $this->setUrl($node->loc->__toString(), $indices);
                }
            }

            if (preg_match('/\n\s*/', $reader->readOuterXml())) {
                return;
            }
        }
    }

    protected function setUrl(string $url, ?string $indices = null): void
    {
        if ($indices) {
            $this->sitemapData->indices[$indices][] = $url;
        }

        $this->sitemapData->urls[] = $url;
    }
}
