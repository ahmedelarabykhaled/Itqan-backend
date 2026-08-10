<?php

namespace App\Filament\Resources\QuranRecitations\Schemas;

use App\Enums\QuranRecitationStatus;
use Filament\Forms\Components\Select;
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
                TextInput::make('name_en')
                    ->label(__('admin.attributes.name_en'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('name_ar')
                    ->label(__('admin.attributes.name_ar'))
                    ->maxLength(255),
                TextInput::make('bitrate')
                    ->maxLength(255),
                TextInput::make('source_url')
                    ->url(),
                Select::make('status')
                    ->options(fn (): array => collect(QuranRecitationStatus::cases())
                        ->mapWithKeys(fn (QuranRecitationStatus $status): array => [
                            $status->value => __("admin.quran_recitation_status.{$status->value}"),
                        ])
                        ->all())
                    ->required()
                    ->default(QuranRecitationStatus::Enabled),
            ]);
    }
}
