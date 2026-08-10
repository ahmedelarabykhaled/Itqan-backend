<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuranRecitationAyahResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'surah' => $this->surah,
            'ayah' => $this->ayah,
            'source_url' => $this->source_url,
            'file_url' => filled($this->file)
                ? Storage::disk('public')->url($this->file)
                : null,
        ];
    }
}
