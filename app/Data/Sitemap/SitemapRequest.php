<?php

namespace App\Data\Sitemap;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class SitemapRequest extends Data
{
    public function __construct(
        public string $sitemapUrl,
    ) {}

    /**
     * @return array<string, array<string>>
     */
    public static function rules(): array
    {
        return [
            'sitemap_url' => [
                'required',
                'url',
            ],
        ];
    }
}
