<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function customTranslations()
    {
        return $this->hasMany(CityTranslation::class);
    }
}
