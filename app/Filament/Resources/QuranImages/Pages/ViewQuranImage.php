<?php

namespace App\Filament\Resources\QuranImages\Pages;

use App\Filament\Resources\QuranImages\QuranImageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuranImage extends ViewRecord
{
    protected static string $resource = QuranImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
