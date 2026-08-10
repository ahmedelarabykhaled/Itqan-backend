<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuranRecitation extends Model
{
    /** @use HasFactory<\Database\Factories\QuranRecitationFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'bitrate',
        'source_url',
    ];

    public function ayahs(): HasMany
    {
        return $this->hasMany(QuranRecitationAyah::class);
    }
}
