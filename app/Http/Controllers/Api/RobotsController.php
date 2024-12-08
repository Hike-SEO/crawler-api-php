<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RobotsRequest;
use App\Services\RobotsService;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(RobotsRequest $request, RobotsService $robotsService): Response
    {
        return response($robotsService->getRobotsTxt($request->websiteUrl));
    }
}
