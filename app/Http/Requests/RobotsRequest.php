<?php

namespace App\Http\Requests;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class RobotsRequest extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
    ) {}

    /**
     * @return array<string, array<string>>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'website_url' => [
                'required',
                'url',
            ],
        ];
    }
}
