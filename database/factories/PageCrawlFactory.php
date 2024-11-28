<?php

namespace Database\Factories;

use App\Data\CrawledPage;
use App\Models\FullCrawl;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageCrawl>
 */
class PageCrawlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url,
            'full_crawl_id' => FullCrawl::factory(),
        ];
    }

    public function withData(?CrawledPage $data = null): static
    {
        return $this->state([
            'data' => $data ?? CrawledPage::fake(),
        ]);
    }
}
