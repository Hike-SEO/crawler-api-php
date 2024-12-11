<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Websites;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Services\WebsiteService;
use Illuminate\Http\Response;

class DeleteController extends Controller
{
    public function __invoke(Website $website, WebsiteService $websiteService): Response
    {
        $websiteService->deleteWebsite($website);

        return response()->noContent();
    }
}
