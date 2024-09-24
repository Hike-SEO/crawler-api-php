<?php

namespace App\Enums;

use HeadlessChromium\Page;

enum PageEvent: string
{
    case DOM_CONTENT_LOADED = Page::DOM_CONTENT_LOADED;
    case FIRST_CONTENTFUL_PAINT = Page::FIRST_CONTENTFUL_PAINT;
    case FIRST_IMAGE_PAINT = Page::FIRST_IMAGE_PAINT;
    case FIRST_MEANINGFUL_PAINT = Page::FIRST_MEANINGFUL_PAINT;
    case FIRST_PAINT = Page::FIRST_PAINT;
    case INIT = Page::INIT;
    case INTERACTIVE_TIME = Page::INTERACTIVE_TIME;
    case LOAD = Page::LOAD;
    case NETWORK_IDLE = Page::NETWORK_IDLE;
}
