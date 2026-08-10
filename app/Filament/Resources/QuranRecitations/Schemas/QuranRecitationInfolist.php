<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QuranRecitationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('slug'),
                TextEntry::make('name'),
                TextEntry::make('bitrate')
                    ->placeholder('-'),
                TextEntry::make('source_url')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
