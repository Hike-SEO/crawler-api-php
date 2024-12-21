<?php

namespace App\Http\Requests;

use App\Enums\PDFFormat;
use App\Enums\PDFMedia;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class PdfRequest extends Data
{
    public function __construct(
        #[MapInputName('website_url')]
        public string $websiteUrl,
        #[MapInputName('wait_until')]
        public string $waitUntil = 'domcontentloaded',
        public PDFFormat $format = PDFFormat::A4,
        public PDFMedia $media = PDFMedia::Print,
        public bool $landscape = false,
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
            'format' => [
                'nullable',
                Rule::enum(PDFFormat::class),
            ],
            'media' => [
                'nullable',
                Rule::enum(PDFMedia::class),
            ],
            'landscape' => [
                'nullable',
                'boolean',
            ],
        ];
    }
}
