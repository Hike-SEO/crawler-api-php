<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Websites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Websites\StoreRequest;
use App\Http\Resources\WebsiteResource;
use App\Services\WebsiteService;
use Illuminate\Http\Resources\Json\JsonResource;

class CreateController extends Controller
{
    public function __invoke(StoreRequest $request, WebsiteService $websiteService): JsonResource
    {
        $website = $websiteService->createWebsite($request);

        return WebsiteResource::make($website);
    }
}
