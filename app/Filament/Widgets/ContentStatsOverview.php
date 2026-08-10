<?php

namespace App\Filament\Widgets;

use App\Models\QuranImage;
use App\Models\QuranImagePart;
use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use App\Models\QuranTranslation;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ContentStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getHeading(): ?string
    {
        return __('admin.content.heading');
    }

    protected function getDescription(): ?string
    {
        return __('admin.content.description');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $imageSets = QuranImage::query()->count();
        $tafaseer = QuranTranslation::query()->count();
        $recitations = QuranRecitation::query()->count();

        return [
            Stat::make(__('admin.content.mushaf_images'), Number::format($imageSets))
                ->description(__('admin.content.mushaf_images_description', [
                    'count' => Number::format(QuranImagePart::query()->count()),
                ]))
                ->descriptionIcon(Heroicon::OutlinedPhoto)
                ->color('info'),

            Stat::make(__('admin.content.tafaseer'), Number::format($tafaseer))
                ->description(__('admin.content.tafaseer_description', [
                    'count' => QuranTranslation::query()->distinct()->count('language_code'),
                ]))
                ->descriptionIcon(Heroicon::OutlinedLanguage)
                ->color('success'),

            Stat::make(__('admin.content.recitations'), Number::format($recitations))
                ->description(__('admin.content.recitations_description', [
                    'count' => Number::format(QuranRecitationAyah::query()->count()),
                ]))
                ->descriptionIcon(Heroicon::OutlinedSpeakerWave)
                ->color('warning'),
        ];
    }
}
