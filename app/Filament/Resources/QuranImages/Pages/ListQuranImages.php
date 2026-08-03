<?php

namespace App\Filament\Resources\QuranImages\Pages;

use App\Filament\Resources\QuranImages\QuranImageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuranImages extends ListRecords
{
    protected static string $resource = QuranImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
