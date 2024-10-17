<?php

namespace App\Http\Controllers\Api\Websites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Websites\CreateRequest;
use App\Http\Resources\WebsiteResource;
use App\Services\WebsiteService;

class CreateController extends Controller
{
    public function __invoke(CreateRequest $request, WebsiteService $websiteService)
    {
        $website = $websiteService->createWebsite($request);

        return WebsiteResource::make($website);
    }
}
