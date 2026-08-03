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
                TextEntry::make('external_id'),
                TextEntry::make('display_name'),
                TextEntry::make('translator')
                    ->placeholder('-'),
                TextEntry::make('translator_foreign')
                    ->placeholder('-'),
                TextEntry::make('language_code'),
                TextEntry::make('file_url')
                    ->placeholder('-'),
                TextEntry::make('file_name')
                    ->placeholder('-'),
                TextEntry::make('save_to')
                    ->placeholder('-'),
                TextEntry::make('download_type')
                    ->placeholder('-'),
                TextEntry::make('minimum_version'),
                TextEntry::make('current_version'),
                TextEntry::make('remote_last_modified')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('file')
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
