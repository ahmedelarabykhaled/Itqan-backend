<?php

namespace App\Filament\Resources\QuranTranslations\Pages;

use App\Filament\Resources\QuranTranslations\QuranTranslationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuranTranslations extends ListRecords
{
    protected static string $resource = QuranTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
