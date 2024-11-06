<?php

declare(strict_types=1);

namespace App\Http\Requests\Websites;

use App\Enums\WaitUntil;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CreateRequest extends Data
{
    public function __construct(
        public string $url,
        public bool $ignoreRobotsTxt = false,
        public WaitUntil $waitUntil = WaitUntil::DOM_CONTENT_LOADED,
        public bool $skipIgnoredPaths = false,
        public int $pageTimeout = 15000,
        public int $maxConcurrentPages = 15,
        public bool $hikeUserAgent = false,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                Rule::unique('websites', 'url')->ignore(request('website')),
            ],
            'ignore_robots_txt' => [
                'sometimes',
                'boolean',
            ],
            'wait_until' => [
                'sometimes',
                Rule::enum(WaitUntil::class),
            ],
            'skip_ignore_paths' => [
                'sometimes',
                'boolean',
            ],
            'page_timeout' => [
                'sometimes',
                'numeric',
                'between:500,30000',
            ],
            'max_concurrent_pages' => [
                'sometimes',
                'numeric',
                'between:1,100',
            ],
            'hike_user_agent' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
