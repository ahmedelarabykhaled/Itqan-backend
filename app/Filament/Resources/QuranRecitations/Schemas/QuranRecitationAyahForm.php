<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use App\Support\QuranAyahCatalog;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                    ->helperText(__('admin.helpers.recitation_zip_file'))
                    ->acceptedFileTypes([
                        'application/zip',
                        'application/x-zip',
                        'application/x-zip-compressed',
                        'application/octet-stream',
                    ])
                    ->disk('public')
                    ->directory(fn ($livewire): string => 'quran-recitation-ayahs/'.$livewire->getOwnerRecord()->slug)
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->nullable()
                    ->getUploadedFileNameForStorageUsing(
                        function (Get $get, TemporaryUploadedFile $file): string {
                            $label = QuranAyahCatalog::fileLabel((int) $get('surah'), (int) $get('ayah'));

                            return "{$label}.zip";
                        }
                    ),
                FileUpload::make('mp3_file')
                    ->label(__('admin.attributes.mp3_file'))
                    ->helperText(__('admin.helpers.recitation_mp3_file'))
                    ->acceptedFileTypes([
                        'audio/mpeg',
                        'audio/mp3',
                        'audio/x-mpeg',
                        'audio/x-mp3',
                        'audio/mpeg3',
                        'application/octet-stream',
                    ])
                    ->disk('public')
                    ->directory(fn ($livewire): string => 'quran-recitation-ayahs/'.$livewire->getOwnerRecord()->slug)
                    ->visibility('public')
                    ->downloadable()
                    ->openable()
                    ->nullable()
                    ->getUploadedFileNameForStorageUsing(
                        function (Get $get, TemporaryUploadedFile $file): string {
                            $label = QuranAyahCatalog::fileLabel((int) $get('surah'), (int) $get('ayah'));

                            return "{$label}.mp3";
                        }
                    ),
            ]);
    }
}
