<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use App\Enums\QuranRecitationStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QuranRecitationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('slug'),
                TextEntry::make('name_en')
                    ->label(__('admin.attributes.name_en')),
                TextEntry::make('name_ar')
                    ->label(__('admin.attributes.name_ar'))
                    ->placeholder('-'),
                TextEntry::make('bitrate')
                    ->placeholder('-'),
                TextEntry::make('source_url')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn (QuranRecitationStatus $state): string => __("admin.quran_recitation_status.{$state->value}")),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
