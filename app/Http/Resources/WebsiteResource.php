<?php

namespace App\Http\Resources;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Website
 */
class WebsiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getRouteKey(),
            'url' => $this->url,
        ];
    }
}
