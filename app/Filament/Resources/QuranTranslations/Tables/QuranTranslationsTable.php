<?php

namespace App\Filament\Resources\QuranTranslations\Tables;

use App\Models\QuranTranslation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class QuranTranslationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('external_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('display_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('language_code')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('translator')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('current_version')
                    ->label('Version')
                    ->sortable(),
                TextColumn::make('file')
                    ->label('File')
                    ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-')
                    ->url(fn (?string $state): ?string => $state ? Storage::disk('public')->url($state) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('remote_last_modified')
                    ->label('Remote Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('language_code')
                    ->label('Language')
                    ->options(fn (): array => QuranTranslation::query()
                        ->distinct()
                        ->orderBy('language_code')
                        ->pluck('language_code', 'language_code')
                        ->all()),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->visible(fn (QuranTranslation $record): bool => filled($record->file) && Storage::disk('public')->exists($record->file))
                    ->action(fn (QuranTranslation $record) => Storage::disk('public')->download(
                        $record->file,
                        $record->file_name ?: basename($record->file),
                    )),
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
