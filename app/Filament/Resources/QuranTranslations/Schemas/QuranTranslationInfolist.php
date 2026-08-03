<?php

namespace App\Filament\Resources\QuranTranslations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class QuranTranslationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('external_id')
                    ->label('External ID'),
                TextEntry::make('display_name'),
                TextEntry::make('translator')
                    ->placeholder('-'),
                TextEntry::make('translator_foreign')
                    ->label('Translator (Foreign)')
                    ->placeholder('-'),
                TextEntry::make('language_code'),
                TextEntry::make('file_url')
                    ->label('File URL')
                    ->placeholder('-'),
                TextEntry::make('file_name')
                    ->label('File Name')
                    ->placeholder('-'),
                TextEntry::make('save_to')
                    ->placeholder('-'),
                TextEntry::make('download_type')
                    ->placeholder('-'),
                TextEntry::make('minimum_version'),
                TextEntry::make('current_version'),
                TextEntry::make('remote_last_modified')
                    ->label('Remote Last Modified')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('file')
                    ->label('File')
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                    ->openUrlInNewTab()
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
