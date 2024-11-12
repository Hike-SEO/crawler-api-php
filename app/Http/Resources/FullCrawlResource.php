<?php

namespace App\Http\Resources;

use App\Models\FullCrawl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FullCrawl
 */
class FullCrawlResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'website' => WebsiteResource::make($this->website),
            'pages' => $this->pageCrawls,
        ];
    }
}
