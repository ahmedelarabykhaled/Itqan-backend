<?php

namespace App\Filament\Resources\QuranImages\Pages;

use App\Filament\Resources\QuranImages\QuranImageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuranImage extends EditRecord
{
    protected static string $resource = QuranImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
