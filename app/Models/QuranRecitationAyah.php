<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuranRecitationAyah extends Model
{
    /** @use HasFactory<\Database\Factories\QuranRecitationAyahFactory> */
    use HasFactory;

    protected $fillable = [
        'quran_recitation_id',
        'surah',
        'ayah',
        'source_url',
        'file',
        'mp3_file',
    ];

    public function quranRecitation(): BelongsTo
    {
        return $this->belongsTo(QuranRecitation::class);
    }
}
