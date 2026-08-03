<?php

namespace App\Filament\Resources\QuranImages\RelationManagers;

use App\Filament\Resources\QuranImages\Schemas\QuranImagePartForm;
use App\Filament\Resources\QuranImages\Tables\QuranImagePartsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PartsRelationManager extends RelationManager
{
    protected static string $relationship = 'parts';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relations.quran_image_parts');
    }

    public function form(Schema $schema): Schema
    {
        return QuranImagePartForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return QuranImagePartsTable::configure(
            $table->headerActions([
                CreateAction::make(),
            ]),
        );
    }
}
