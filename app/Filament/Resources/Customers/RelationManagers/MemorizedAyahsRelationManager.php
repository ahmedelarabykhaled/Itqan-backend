<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use App\Filament\Resources\Customers\Tables\MemorizedAyahsTable;
use App\Models\Customer;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MemorizedAyahsRelationManager extends RelationManager
{
    protected static string $relationship = 'memorizedAyahs';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relations.memorized_ayahs');
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        if (! $ownerRecord instanceof Customer) {
            return null;
        }

        $count = $ownerRecord->memorizedAyahs()->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'primary';
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return MemorizedAyahsTable::configure($table);
    }
}
