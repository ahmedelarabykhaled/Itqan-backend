<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranImagePart extends Model
{
    /** @use HasFactory<\Database\Factories\QuranImagePartFactory> */
    use HasFactory;

    protected $fillable = [
        'quran_image_id',
        'source_url',
        'file',
    ];

    public function quranImage(): BelongsTo
    {
        return $this->belongsTo(QuranImage::class);
    }
}
