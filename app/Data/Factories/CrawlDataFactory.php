<?php

namespace App\Data\Factories;

use App\Data\CrawlData;
use PHPHtmlParser\Dom;
use Psr\Http\Message\ResponseInterface;

class CrawlDataFactory
{
    public function fromResponse(string $url, ResponseInterface $response): CrawlData
    {
        $html = $response->getBody()->getContents();

        $dom = new Dom;
        $dom->loadStr($html);

        return CrawlData::from([
            'url' => $url,
            'h1s' => $this->getInnerHtmls($dom->find('h1')),
            'h2s' => $this->getInnerHtmls($dom->find('h2')),
            'h3s' => $this->getInnerHtmls($dom->find('h3')),
            'p' => $this->getInnerHtmls($dom->find('p')),
        ]);
    }

    public function parsePerformance(CrawlData $crawlData, $performance): CrawlData
    {
        // TODO
        return $crawlData;
    }

    private function getInnerHtmls(Dom\Collection $collection): array
    {
        return collect($collection->toArray())
            ->map(fn (Dom\HtmlNode $node) => $node->innerHtml())
            ->map('strip_tags')
            ->filter()
            ->values()
            ->all();
    }
}
