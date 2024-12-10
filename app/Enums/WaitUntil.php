<?php

namespace App\Enums;

enum WaitUntil: string
{
    case DOM_CONTENT_LOADED = 'domcontentloaded';
    case LOAD = 'load';
    case NETWORK_IDLE0 = 'networkidle0';
    case NETWORK_IDLE2 = 'networkidle2';
}
