<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PdfRequest;
use App\Services\CrawlService;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function __construct(
        private readonly CrawlService $crawlService,
    ) {}

    public function __invoke(PdfRequest $request): Response
    {
        $pdf = $this->crawlService->getPdf($request);

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="website.pdf"');
    }
}
