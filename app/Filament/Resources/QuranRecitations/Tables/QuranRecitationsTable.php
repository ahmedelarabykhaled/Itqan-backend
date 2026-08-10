<?php

namespace App\Filament\Resources\QuranRecitations\Tables;

use App\Enums\QuranRecitationStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuranRecitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_en')
                    ->label(__('admin.attributes.name_en'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label(__('admin.attributes.name_ar'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (QuranRecitationStatus $state): string => __("admin.quran_recitation_status.{$state->value}"))
                    ->color(fn (QuranRecitationStatus $state): string => match ($state) {
                        QuranRecitationStatus::Enabled => 'success',
                        QuranRecitationStatus::Disabled => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('bitrate')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('source_url')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('ayahs_count')
                    ->counts('ayahs')
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
                SelectFilter::make('status')
                    ->options(fn (): array => collect(QuranRecitationStatus::cases())
                        ->mapWithKeys(fn (QuranRecitationStatus $status): array => [
                            $status->value => __("admin.quran_recitation_status.{$status->value}"),
                        ])
                        ->all()),
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
