<?php

namespace App\Http\Requests;

use App\Enums\PageEvent;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SingleCrawlRequest extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
        #[MapInputName('wait_until')]
        public PageEvent $waitUntil = PageEvent::DOM_CONTENT_LOADED,
        public bool $performance = true,
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'website_url' => [
                'required',
                'url',
            ],
            'wait_until' => [
                'nullable',
                Rule::enum(PageEvent::class),
            ],
            'performance' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
