<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MemorizedAyahsTable
{
    /**
     * Statuses currently used by the mobile app API.
     *
     * @var list<string>
     */
    public const STATUSES = [
        'memorized',
        'bookmarked',
        'saved',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('memorized_at', 'desc')
            ->columns([
                TextColumn::make('surah_id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ayah_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('statuses')
                    ->badge()
                    ->placeholder('-')
                    ->formatStateUsing(fn (?string $state): ?string => filled($state)
                        ? __("admin.memorized_statuses.{$state}")
                        : null)
                    ->color(fn (?string $state): string => match ($state) {
                        'memorized' => 'success',
                        'bookmarked' => 'warning',
                        'saved' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('memorized_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.attributes.statuses'))
                    ->options(collect(self::STATUSES)
                        ->mapWithKeys(fn (string $status): array => [
                            $status => __("admin.memorized_statuses.{$status}"),
                        ])
                        ->all())
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereJsonContains('statuses', $data['value']);
                    }),
                SelectFilter::make('surah_id')
                    ->options(fn (): array => self::surahOptions())
                    ->searchable(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    /**
     * @return array<int|string, string>
     */
    protected static function surahOptions(): array
    {
        return collect(range(1, 114))
            ->mapWithKeys(fn (int $surahId): array => [
                $surahId => __('admin.attributes.surah_id').' '.$surahId,
            ])
            ->all();
    }
}
