<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    protected $fillable = [
        'surah_id',
        'total_ayahs',
    ];
}
