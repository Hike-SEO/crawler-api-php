<?php

namespace App\Data\Factories;

use App\Data\CrawlData;
use HeadlessChromium\Page;
use PHPHtmlParser\Dom;

class CrawlDataFactory
{
    public function fromPage(Page $page): CrawlData
    {
        $html = $page->getHtml();

        $dom = new Dom;
        $dom->loadStr($html);

        return CrawlData::from([
            'url' => $page->getCurrentUrl(),
            'h1s' => $this->getInnerHtmls($dom->find('h1')),
            'h2s' => $this->getInnerHtmls($dom->find('h2')),
            'h3s' => $this->getInnerHtmls($dom->find('h3')),
            'p' => $this->getInnerHtmls($dom->find('p')),
        ]);
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
