<?php

namespace App\Services;

use App\Data\CrawlData;
use App\Data\Factories\CrawlDataFactory;
use App\Http\Requests\SingleCrawlRequest;
use HeadlessChromium\BrowserFactory;

class CrawlService
{
    public function __construct(
        private readonly CrawlDataFactory $crawlDataFactory,
    ) {}

    public function crawlUrl(SingleCrawlRequest $request): CrawlData
    {
        $browserFactory = new BrowserFactory('google-chrome-stable');

        $browser = $browserFactory->createBrowser([
            'noSandbox' => true,
        ]);

        try {
            $page = $browser->createPage();

            $responses = collect();
            $page->getSession()->on('method:Network.responseReceived',
                function ($params) use (&$responses) {
                    $responses->push($params['response']);
                }
            );

            $page->navigate($request->websiteUrl)->waitForNavigation($request->waitUntil->value);

            $data = $this->crawlDataFactory->fromPage($page);

            $browser->close();

            return $data;
        } catch (\Throwable $e) {
            $browser->close();

            throw $e;
        }
    }
}
