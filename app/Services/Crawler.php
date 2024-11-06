<?php

namespace App\Services;

use Spatie\Crawler\Crawler as BaseCrawler;

/**
 * We extend the main crawler to make it compatible with mocking via the app container.
 */
class Crawler extends BaseCrawler {}
