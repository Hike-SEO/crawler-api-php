<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Websites;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteResource;
use App\Models\Website;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        $websites = Website::query()->orderByDesc('id')->get();

        return WebsiteResource::collection($websites);
    }
}
