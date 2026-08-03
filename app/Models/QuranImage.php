<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuranImage extends Model
{
    /** @use HasFactory<\Database\Factories\QuranImageFactory> */
    use HasFactory;

    protected $fillable = [
        'width',
        'source_url',
        'file',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(QuranImagePart::class);
    }
}
