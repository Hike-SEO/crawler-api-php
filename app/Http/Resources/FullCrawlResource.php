<?php

namespace App\Http\Resources;

use App\Models\FullCrawl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FullCrawlResource extends JsonResource
{
    /**
     * @mixin FullCrawl
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'website' => WebsiteResource::make($this->website),
            'pages' => $this->pageCrawls,
        ];
    }
}
