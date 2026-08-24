<?php

namespace App\Models;

use App\Enums\QuranRecitationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class QuranRecitation extends Model
{
    /** @use HasFactory<\Database\Factories\QuranRecitationFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleted(function (QuranRecitation $recitation): void {
            if (filled($recitation->slug)) {
                Storage::disk('public')->deleteDirectory("quran-recitation-ayahs/{$recitation->slug}");
            }
        });
    }

    protected $fillable = [
        'slug',
        'name_en',
        'name_ar',
        'bitrate',
        'source_url',
        'status',
    ];

    public function ayahs(): HasMany
    {
        return $this->hasMany(QuranRecitationAyah::class);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeEnabled(Builder $query): void
    {
        $query->where('status', QuranRecitationStatus::Enabled);
    }

    public function isEnabled(): bool
    {
        return $this->status === QuranRecitationStatus::Enabled;
    }

    public function localizedName(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        if ($locale === 'ar' && filled($this->name_ar)) {
            return $this->name_ar;
        }

        if (filled($this->name_en)) {
            return $this->name_en;
        }

        return $this->name_ar ?? '';
    }

    protected function casts(): array
    {
        return [
            'status' => QuranRecitationStatus::class,
        ];
    }
}
