<?php

declare(strict_types=1);

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

    public function updateWebsite(Website $website, CreateRequest $request): Website
    {
        $website->update($request->toArray());

        return $website;
    }
}
