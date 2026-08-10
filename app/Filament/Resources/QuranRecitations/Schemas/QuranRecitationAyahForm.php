<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuranRecitationAyahForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('surah')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(114),
                TextInput::make('ayah')
                    ->numeric()
                    ->required()
                    ->minValue(0),
                TextInput::make('source_url')
                    ->url(),
                FileUpload::make('file')
                    ->label(__('admin.attributes.file'))
                    ->acceptedFileTypes([
                        'application/zip',
                        'application/x-zip-compressed',
                    ])
                    ->disk('public')
                    ->directory('quran-recitation-ayahs')
                    ->visibility('public')
                    ->downloadable(),
                FileUpload::make('mp3_file')
                    ->label(__('admin.attributes.mp3_file'))
                    ->acceptedFileTypes([
                        'audio/mpeg',
                        'audio/mp3',
                    ])
                    ->disk('public')
                    ->directory('quran-recitation-ayahs')
                    ->visibility('public')
                    ->downloadable(),
            ]);
    }
}
