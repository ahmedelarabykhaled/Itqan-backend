<?php

namespace App\Filament\Resources\QuranRecitations;

use App\Filament\Resources\QuranRecitations\Pages\CreateQuranRecitation;
use App\Filament\Resources\QuranRecitations\Pages\EditQuranRecitation;
use App\Filament\Resources\QuranRecitations\Pages\ListQuranRecitations;
use App\Filament\Resources\QuranRecitations\Pages\ViewQuranRecitation;
use App\Filament\Resources\QuranRecitations\RelationManagers\AyahsRelationManager;
use App\Filament\Resources\QuranRecitations\Schemas\QuranRecitationForm;
use App\Filament\Resources\QuranRecitations\Schemas\QuranRecitationInfolist;
use App\Filament\Resources\QuranRecitations\Tables\QuranRecitationsTable;
use App\Models\QuranRecitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuranRecitationResource extends Resource
{
    protected static ?string $model = QuranRecitation::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    public static function getModelLabel(): string
    {
        return __('admin.resources.quran_recitation.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.quran_recitation.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return QuranRecitationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuranRecitationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuranRecitationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AyahsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuranRecitations::route('/'),
            'create' => CreateQuranRecitation::route('/create'),
            'view' => ViewQuranRecitation::route('/{record}'),
            'edit' => EditQuranRecitation::route('/{record}/edit'),
        ];
    }
}
