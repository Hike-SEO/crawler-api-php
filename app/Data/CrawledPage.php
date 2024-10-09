<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class CrawledPage extends Data
{
    /**
     * @param array<int,string> $h1_headings
     * @param array<int,string> $h2_headings
     * @param array<int,string> $h3_headings
     * @param array<int,string> $redirects_to
     */
    public function __construct(
        public string $url,
        public ?string $path,
        public ?string $title,
        public int $word_count,
        public ?string $content,
        /** @var null|DataCollection<int, CrawledPageImage> */
        public ?DataCollection $images,
        /** @var null|DataCollection<int, CrawledPageLink> */
        public ?DataCollection $internal_links,
        /** @var null|DataCollection<int, CrawledPageLink> */
        public ?DataCollection $external_links,
        /** @var null|DataCollection<int, SimilarPage> */
        public ?DataCollection $similar_pages,
        public array|Optional $redirects_to,
        public string|Optional $redirect_from,
        public ?int $response_code = null,
        public ?string $meta_robots = null,
        public ?string $meta_desc = null,
        public ?string $meta_keywords = null,
        public ?string $optimiser = null,
        public ?string $canonical_link = null,
        public array $h1_headings = [],
        public array $h2_headings = [],
        public array $h3_headings = [],
        /** @var null|DataCollection<int, CrawledPageMeta> */
        public ?DataCollection $opengraph_meta = null,
        public ?CrawlDataMetrics $metrics = null,
    ) {}
}
