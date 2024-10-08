<?php

namespace App\Data\Factories;

use App\Data\CrawledPage;
use App\Data\CrawledPageImage;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\HtmlNode;
use Psr\Http\Message\ResponseInterface;

class CrawlDataFactory
{
    public function fromResponse(string $url, ResponseInterface $response): CrawledPage
    {
        $html = $response->getBody()->getContents();

        $dom = new Dom;
        $dom->loadStr($html);

        $uri = new Uri($url);
        $path = $uri::composeComponents(
            null,
            null,
            path: $uri->getPath(),
            query: $uri->getQuery(),
            fragment: $uri->getFragment(),
        );

        /** @var null|HtmlNode $titleNode */
        $titleNode = $dom->find('title', 0);

        return CrawledPage::from([
            'response_code' => $response->getStatusCode(),
            'url' => $uri->__toString(),
            'path' => $path,
            'title' => $titleNode?->innerHtml() ?? '',
            'content' => collect($this->getInnerHtmls($dom->find('p')))->map('strip_tags')->join('. '),
            'h1_headings' => $this->getInnerHtmls($dom->find('h1')),
            'h2_headings' => $this->getInnerHtmls($dom->find('h2')),
            'h3_headings' => $this->getInnerHtmls($dom->find('h3')),
            'images' => $this->getImagesFromDom($dom),
        ]);
    }

    /**
     * @return Collection<int, CrawledPageImage>
     */
    public function getImagesFromDom(Dom $dom): Collection
    {
        /** @var Dom\Collection $nodes */
        $nodes = $dom->find('img');

        return collect($nodes->toArray())->map(function (HtmlNode $node) {
            return CrawledPageImage::from([
                'src' => $node->getAttribute('src'),
                'alt' => $node->getAttribute('alt'),
            ]);
        });
    }

    /**
     * @param array<string, mixed> $performance
     */
    public function parsePerformance(CrawledPage $crawlData, array $performance): CrawledPage
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
