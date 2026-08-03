<?php

namespace App\Filament\Resources\QuranTranslations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuranTranslationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('external_id')
                    ->label('External ID')
                    ->numeric()
                    ->required()
                    ->minValue(1),
                TextInput::make('display_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('translator')
                    ->maxLength(255),
                TextInput::make('translator_foreign')
                    ->label('Translator (Foreign)')
                    ->maxLength(255),
                TextInput::make('language_code')
                    ->required()
                    ->maxLength(10),
                TextInput::make('file_url')
                    ->label('File URL')
                    ->url()
                    ->required()
                    ->maxLength(255),
                TextInput::make('file_name')
                    ->label('File Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('save_to')
                    ->maxLength(255),
                TextInput::make('download_type')
                    ->maxLength(255),
                TextInput::make('minimum_version')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('current_version')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                DateTimePicker::make('remote_last_modified')
                    ->label('Remote Last Modified'),
                FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes([
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/octet-stream',
                        'application/x-sqlite3',
                    ])
                    ->disk('public')
                    ->directory('quran-translations')
                    ->visibility('public')
                    ->downloadable(),
            ]);
    }
}
