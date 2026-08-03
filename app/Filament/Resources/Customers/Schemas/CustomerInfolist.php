<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\Customer;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('gender')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): ?string => $state ? __("admin.gender.{$state}") : null)
                    ->placeholder('-'),
                TextEntry::make('avatar')
                    ->placeholder('-'),
                TextEntry::make('provider')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('provider_id')
                    ->placeholder('-'),
                TextEntry::make('email_verified_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('memorized_ayahs_count')
                    ->label(__('admin.customer.memorized_count'))
                    ->state(fn (Customer $record): int => $record->memorizedAyahs()->count()),
                TextEntry::make('last_memorized_at')
                    ->label(__('admin.customer.last_memorized_at'))
                    ->state(fn (Customer $record) => $record->memorizedAyahs()->max('memorized_at'))
                    ->dateTime()
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
