<?php

namespace App\Services;

use App\Http\Requests\Websites\CreateRequest;
use App\Models\Website;

class WebsiteService
{
    public function createWebsite(CreateRequest $request): Website
    {
        /** @var Website $website */
        $website = Website::query()->create($request->toArray());

        return $website;
    }

    public function updateWebsite(Website $website, CreateRequest $request): Website
    {
        $website->update($request->toArray());

        return $website;
    }
}
