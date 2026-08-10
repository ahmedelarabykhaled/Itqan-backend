<?php

namespace App\Filament\Resources\QuranRecitations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class QuranRecitationAyahsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('surah')
                    ->sortable(),
                TextColumn::make('ayah')
                    ->sortable(),
                TextColumn::make('source_url')
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('file')
                    ->label(__('admin.attributes.file'))
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('mp3_file')
                    ->label(__('admin.attributes.mp3_file'))
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::FiveExtraLarge)
                    ->modalHeading(__('admin.actions.edit_recitation_ayah')),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
