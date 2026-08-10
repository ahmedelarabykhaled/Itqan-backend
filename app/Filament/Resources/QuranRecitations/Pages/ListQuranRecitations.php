<?php

namespace App\Filament\Resources\QuranRecitations\Pages;

use App\Filament\Resources\QuranRecitations\QuranRecitationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuranRecitations extends ListRecords
{
    protected static string $resource = QuranRecitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
