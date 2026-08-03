<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMemorizedAyah extends Model
{
    protected $fillable = [
        'user_id',
        'surah_id',
        'ayah_number',
        'memorized_at',
        'statuses',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'memorized_at' => 'datetime',
            'statuses' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function surah(): BelongsTo
    {
        return $this->belongsTo(Surah::class, 'surah_id', 'surah_id');
    }
}
