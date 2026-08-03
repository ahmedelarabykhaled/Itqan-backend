<?php

namespace App\Filament\Resources\QuranImages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuranImagePartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source_url')
                    ->label('Source URL')
                    ->url(),
                FileUpload::make('file')
                    ->label('File')
                    ->acceptedFileTypes([
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/gzip',
                        'application/x-gzip',
                        'application/x-tar',
                        'application/x-7z-compressed',
                        'application/vnd.rar',
                        'application/x-rar-compressed',
                    ])
                    ->disk('public')
                    ->directory('quran-image-parts')
                    ->visibility('public')
                    ->downloadable(),
            ]);
    }
}
