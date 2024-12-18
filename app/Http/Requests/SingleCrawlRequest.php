<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SingleCrawlRequest extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
        #[MapInputName('wait_until')]
        public string $waitUntil = 'domcontentloaded',
        public bool $performance = true,
    ) {}

    /**
     * @return array<string, array<string|Enum>>
     */
    public static function rules(ValidationContext $context): array
    {
        return [
            'website_url' => [
                'required',
                'url',
            ],
            'wait_until' => [
                'nullable',
                Rule::in(['domcontentloaded', 'load', 'networkidle0', 'networkidle2']),
            ],
            'performance' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
