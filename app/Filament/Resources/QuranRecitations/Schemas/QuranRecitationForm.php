<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuranRecitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bitrate')
                    ->maxLength(255),
                TextInput::make('source_url')
                    ->url(),
            ]);
    }
}
