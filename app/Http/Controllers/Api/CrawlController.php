<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use HeadlessChromium\BrowserFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PHPHtmlParser\Dom;

class CrawlController extends Controller
{
    public function __invoke(Request $request)
    {
        $url = $request->string('url');

//        $html = Cache::remember(
//            $request->string('url'),
//            now()->addHour(),
//            function () use ($url) {
                $browserFactory = new BrowserFactory('google-chrome-stable');

                $browser = $browserFactory->createBrowser([
                    'noSandbox' => true,
                ]);

                try {
                    $page = $browser->createPage();

                    $responses = collect();
                    $page->getSession()->on("method:Network.responseReceived",
                        function($params) use (&$responses)
                        {
                            $responses->push($params["response"]);
                        }
                    );

                    $page->navigate($url)->waitForNavigation();
                    $html = $page->getHtml();
                    $browser->close();

//                    dd($responses->where('mimeType', 'text/html'));

//                    return $html;
                } catch (\Throwable $e) {
                    $browser->close();

                    throw $e;
                }
//            });

        $dom = new Dom();
        $dom->loadStr($html);


        $h1s = $dom->find('h1');
        $h2s = $dom->find('h2');

        return [
            'h1s' => $this->getInnerHtmls($dom->find('h1')),
            'h2s' => $this->getInnerHtmls($dom->find('h2')),
            'h3s' => $this->getInnerHtmls($dom->find('h3')),
            'p' => $this->getInnerHtmls($dom->find('p')),
        ];
    }

    private function getInnerHtmls(Dom\Collection $collection): array
    {
        return collect($collection->toArray())
            ->map(fn(Dom\HtmlNode $node) => $node->innerHtml())
            ->map('strip_tags')
            ->filter()
            ->values()
            ->all();
    }
}
