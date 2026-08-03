<?php

namespace App\Filament\Resources\QuranImages;

use App\Filament\Resources\QuranImages\Pages\CreateQuranImage;
use App\Filament\Resources\QuranImages\Pages\EditQuranImage;
use App\Filament\Resources\QuranImages\Pages\ListQuranImages;
use App\Filament\Resources\QuranImages\Pages\ViewQuranImage;
use App\Filament\Resources\QuranImages\RelationManagers\PartsRelationManager;
use App\Filament\Resources\QuranImages\Schemas\QuranImageForm;
use App\Filament\Resources\QuranImages\Schemas\QuranImageInfolist;
use App\Filament\Resources\QuranImages\Tables\QuranImagesTable;
use App\Models\QuranImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuranImageResource extends Resource
{
    protected static ?string $model = QuranImage::class;

    protected static ?string $recordTitleAttribute = 'width';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function getModelLabel(): string
    {
        return __('admin.resources.quran_image.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.quran_image.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return QuranImageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuranImageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuranImagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PartsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuranImages::route('/'),
            'create' => CreateQuranImage::route('/create'),
            'view' => ViewQuranImage::route('/{record}'),
            'edit' => EditQuranImage::route('/{record}/edit'),
        ];
    }
}
