<?php

namespace App\Filament\Resources\QuranTranslations\Pages;

use App\Filament\Resources\QuranTranslations\QuranTranslationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuranTranslation extends EditRecord
{
    protected static string $resource = QuranTranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
