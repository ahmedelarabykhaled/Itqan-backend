<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuranRecitationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->localizedName(),
            'bitrate' => $this->bitrate,
            'source_url' => $this->source_url,
            'ayahs_count' => $this->whenCounted('ayahs'),
        ];
    }
}
