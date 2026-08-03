<?php

namespace App\Filament\Resources\QuranTranslations;

use App\Filament\Resources\QuranTranslations\Pages\CreateQuranTranslation;
use App\Filament\Resources\QuranTranslations\Pages\EditQuranTranslation;
use App\Filament\Resources\QuranTranslations\Pages\ListQuranTranslations;
use App\Filament\Resources\QuranTranslations\Pages\ViewQuranTranslation;
use App\Filament\Resources\QuranTranslations\Schemas\QuranTranslationForm;
use App\Filament\Resources\QuranTranslations\Schemas\QuranTranslationInfolist;
use App\Filament\Resources\QuranTranslations\Tables\QuranTranslationsTable;
use App\Models\QuranTranslation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuranTranslationResource extends Resource
{
    protected static ?string $model = QuranTranslation::class;

    protected static ?string $recordTitleAttribute = 'display_name';

    protected static ?string $navigationLabel = 'Quran Translations';

    protected static ?string $modelLabel = 'Quran Translation';

    protected static ?string $pluralModelLabel = 'Quran Translations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public static function form(Schema $schema): Schema
    {
        return QuranTranslationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuranTranslationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuranTranslationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuranTranslations::route('/'),
            'create' => CreateQuranTranslation::route('/create'),
            'view' => ViewQuranTranslation::route('/{record}'),
            'edit' => EditQuranTranslation::route('/{record}/edit'),
        ];
    }
}
