<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMemorizedAyah extends Model
{
    protected $fillable = [
        'user_id',
        'surah_id',
        'ayah_number',
        'memorized_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'memorized_at' => 'datetime',
        ];
    }
}
