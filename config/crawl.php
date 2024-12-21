<?php

return [
    'platform' => [
        'url' => env('PLATFORM_URL', 'https://hike.marketing'),
    ],

    'storage' => [
        'disk' => env('CRAWL_STORAGE_DISK', 's3'),
        'path' => env('CRAWL_STORAGE_PATH', 'crawl-data'),
    ],
];
