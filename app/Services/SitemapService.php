<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use XMLReader;

class SitemapService
{
    public array $urls = [];

    public function fetch(string $url): array
    {
        $this->crawl($url);

        return $this->urls;
    }

    protected function crawl(string $url): void
    {
        try {
            throw new \Exception();
            $response = Http::get($url)->throw()->body();

            $this->parse($response);
        } catch (\Exception) {
            // TODO: Add exception capture

            return;
        }
    }

    protected function parse(string $response)
    {
        $reader = XMLReader::XML($response);

        while ($reader->read()) {
            // If sitemap index we need to follow and return the urls in the next sitemap
            $xml = new SimpleXMLElement($reader->readOuterXML());

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'sitemapindex') {
                foreach ($xml->sitemap as $sitemap) {
                    $sitemapChildResponse = Http::get($sitemap->loc->__toString());

                    $this->parse($sitemapChildResponse->body());
                }
            }

            if ($reader->nodeType == XMLReader::ELEMENT && $reader->name == 'urlset') {
                foreach ($xml as $node) {
                    $this->setUrl($node->loc->__toString());
                }
            }

            if (preg_match('/\n\s*/', $reader->readOuterXml())) {
                return;
            }
        }
    }

    protected function setUrl(string $url): void
    {
        $this->urls[] = $url;
    }
}
