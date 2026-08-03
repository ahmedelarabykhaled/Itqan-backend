<?php

namespace App\Filament\Resources\QuranTranslations\Pages;

use App\Filament\Resources\QuranTranslations\QuranTranslationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuranTranslation extends ViewRecord
{
    protected static string $resource = QuranTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
