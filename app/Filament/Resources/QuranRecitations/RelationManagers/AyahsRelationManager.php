<?php

namespace App\Filament\Resources\QuranRecitations\RelationManagers;

use App\Filament\Resources\QuranRecitations\Schemas\QuranRecitationAyahForm;
use App\Filament\Resources\QuranRecitations\Tables\QuranRecitationAyahsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AyahsRelationManager extends RelationManager
{
    protected static string $relationship = 'ayahs';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.relations.quran_recitation_ayahs');
    }

    public function form(Schema $schema): Schema
    {
        return QuranRecitationAyahForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return QuranRecitationAyahsTable::configure(
            $table->headerActions([
                CreateAction::make(),
            ]),
        );
    }
}
