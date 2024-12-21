<?php

namespace App\Http\Requests;

use App\Enums\ScreenshotViewport;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class ScreenshotRequest extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
        #[MapInputName('wait_until')]
        public string $waitUntil = 'domcontentloaded',
        public ScreenshotViewport $viewport = ScreenshotViewport::Desktop,
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
            'viewport' => [
                'nullable',
                Rule::enum(ScreenshotViewport::class),
            ],
        ];
    }
}
