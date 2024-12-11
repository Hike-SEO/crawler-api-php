<?php

return [
    'storage' => [
        'disk' => env('CAPTURE_STORAGE_DISK', 's3'),
        'screenshot_path' => env('SCREENSHOT_STORAGE_PATH', 'screenshots'),
    ],
];
