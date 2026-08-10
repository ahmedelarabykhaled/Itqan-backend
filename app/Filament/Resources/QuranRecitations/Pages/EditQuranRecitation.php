<?php

namespace App\Filament\Resources\QuranRecitations\Pages;

use App\Filament\Resources\QuranRecitations\QuranRecitationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuranRecitation extends EditRecord
{
    protected static string $resource = QuranRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
