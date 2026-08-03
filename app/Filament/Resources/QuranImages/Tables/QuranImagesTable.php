<?php

namespace App\Filament\Resources\QuranImages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class QuranImagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('width')
                    ->sortable()
                    ->suffix(' px'),
                TextColumn::make('source_url')
                    ->label('Source URL')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('file')
                    ->label('File')
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('parts_count')
                    ->counts('parts')
                    ->label('Parts')
                    ->sortable(),
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
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
