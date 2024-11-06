<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Websites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Websites\CreateRequest;
use App\Http\Resources\WebsiteResource;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateController extends Controller
{
    public function __invoke(Website $website, CreateRequest $request, WebsiteService $websiteService): JsonResource
    {
        $website = $websiteService->updateWebsite($website, $request);

        return WebsiteResource::make($website);
    }
}
