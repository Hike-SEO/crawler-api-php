<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ScreenshotResult extends Data
{
    public function __construct(
        public string $path,
        public string $filename,
    ) {}
}
