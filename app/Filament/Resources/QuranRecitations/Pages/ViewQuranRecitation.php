<?php

namespace App\Filament\Resources\QuranRecitations\Pages;

use App\Filament\Resources\QuranRecitations\QuranRecitationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuranRecitation extends ViewRecord
{
    protected static string $resource = QuranRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
