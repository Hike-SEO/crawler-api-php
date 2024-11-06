<?php

namespace App\Services;

use App\Http\Requests\Websites\StoreRequest;
use App\Models\Website;

class WebsiteService
{
    public function createWebsite(StoreRequest $request): Website
    {
        /** @var Website $website */
        $website = Website::query()->create($request->toArray());

        return $website;
    }
}
