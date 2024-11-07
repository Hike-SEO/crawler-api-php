<?php

namespace App\Data\Factories;

use App\Data\CrawledPage;
use App\Data\CrawledPageImage;
use App\Data\CrawledPageLink;
use App\Data\CrawledPageMeta;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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

        // Get all content from <p> tags
        $content = collect($this->getInnerHtmls($dom->find('p')))
            ->map(fn (string $content) => rtrim($content, ' '))
            ->join(' ');

        /** @var null|HtmlNode $canonicalNode */
        $canonicalNode = $dom->find('link[rel="canonical"]', 0);

        return CrawledPage::from([
            'response_code' => $response->getStatusCode(),
            'url' => $uri->__toString(),
            'path' => $path,
            'title' => $titleNode?->innerHtml() ?? '',
            'content' => $content,
            'word_count' => Str::wordCount($content),
            'h1_headings' => $this->getInnerHtmls($dom->find('h1')),
            'h2_headings' => $this->getInnerHtmls($dom->find('h2')),
            'h3_headings' => $this->getInnerHtmls($dom->find('h3')),
            'images' => $this->getImagesFromDom($dom),
            'internal_links' => $this->getLinksFromDom($dom, $uri, true),
            'external_links' => $this->getLinksFromDom($dom, $uri, false),
            'meta_robots' => $this->getMetaContent('robots', $dom),
            'meta_desc' => $this->getMetaContent('description', $dom),
            'meta_keywords' => $this->getMetaContent('keywords', $dom),
            'optimiser' => $this->getMetaContent('optimiser', $dom),
            'canonical_link' => $canonicalNode?->getAttribute('href') ?? '',
            'opengraph_meta' => $this->getOpengraphMetas($dom),
        ]);
    }

    public function getMetaContent(string $metaName, Dom $dom): ?string
    {
        /** @var null|HtmlNode $meta */
        $meta = $dom->find('meta[name="'.$metaName.'"]', 0);

        return $meta?->getAttribute('content');
    }

    public function getOpengraphMetas(Dom $dom): Collection
    {
        /** @var Dom\Collection<int, HtmlNode> $nodes */
        $nodes = $dom->find('meta');

        return collect($nodes->toArray())
            ->filter(fn (HtmlNode $node) => Str::startsWith($node->getAttribute('property') ?? '', 'og:'))
            ->map(function (HtmlNode $node) {

                return CrawledPageMeta::from([
                    'property' => $node->getAttribute('property') ?? '',
                    'content' => $node->getAttribute('content') ?? '',
                ]);
            })
            ->values();
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
                'alt' => $node->getAttribute('alt') ?: null,
            ]);
        });
    }

    /**
     * @return Collection<int, CrawledPageLink>
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getLinksFromDom(Dom $dom, Uri $baseUri, bool $shouldMatchHost): Collection
    {
        /** @var Dom\Collection<int, HtmlNode> $nodes */
        $nodes = $dom->find('a');

        return collect($nodes->toArray())
            ->filter(function (HtmlNode $node) use ($shouldMatchHost, $baseUri) {
                $href = $node->getAttribute('href');
                if (! $href || Str::startsWith($href, ['#'])) {
                    return false;
                }

                $href = new Uri($href);

                return $shouldMatchHost === ($href->getHost() === $baseUri->getHost() || $href->getHost() === '');
            })->map(function (HtmlNode $node) {
                $href = $node->getAttribute('href');
                $href = new Uri($href);

                return CrawledPageLink::from([
                    'url' => $href->__toString(),
                    'rel' => $node->getAttribute('rel'),
                    'anchor' => [
                        'nodeName' => $node->getTag()->name(),
                        'content' => $node->text(),
                    ],
                ]);
            })->values();
    }

    /**
     * @param array<string, mixed> $performance
     */
    public function parsePerformance(CrawledPage $crawlData, array $performance): CrawledPage
    {
        // TODO https://hikeseo.atlassian.net/browse/CRAW-218
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
