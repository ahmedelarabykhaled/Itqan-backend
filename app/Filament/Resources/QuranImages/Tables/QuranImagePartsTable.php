<?php

namespace App\Filament\Resources\QuranImages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class QuranImagePartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_url')
                    ->label('Source URL')
                    // ->limit(40)
                    ->copyable()
                    ->copyMessage('Copied!')
                    // ->openUrlInNewTab(true)
                    // ->linkToUrl(fn (?string $state): ?string => $state ? url($state) : null)
                    ->toggleable(),
                TextColumn::make('file')
                    ->label('File')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
