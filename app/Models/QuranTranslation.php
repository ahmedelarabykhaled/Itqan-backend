<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuranTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\QuranTranslationFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'display_name',
        'translator',
        'translator_foreign',
        'language_code',
        'file_url',
        'file_name',
        'save_to',
        'download_type',
        'minimum_version',
        'current_version',
        'remote_last_modified',
        'file',
    ];

    protected function casts(): array
    {
        return [
            'external_id' => 'integer',
            'minimum_version' => 'integer',
            'current_version' => 'integer',
            'remote_last_modified' => 'datetime',
        ];
    }
}
