<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\CreateBlogCategory;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\EditBlogCategory;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\ListBlogCategories;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Schemas\BlogCategoryForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Tables\BlogCategoriesTable;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BlogCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogCategories::route('/'),
            'create' => CreateBlogCategory::route('/create'),
            'edit' => EditBlogCategory::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<BlogCategory>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
