<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class SitemapData extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
    ) {}

    /**
     * @return array<string, array<string>>
     */
    public static function rules(): array
    {
        return [
            'website_url' => [
                'required',
                'url',
            ],
        ];
    }
}
