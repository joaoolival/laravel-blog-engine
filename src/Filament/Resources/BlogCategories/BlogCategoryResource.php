<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\CreateBlogCategory;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\EditBlogCategory;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages\ListBlogCategories;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Schemas\BlogCategoryForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Tables\BlogCategoriesTable;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return config('laravel-blog-engine.resources.categories.label', 'Blog Category');
    }

    public static function getPluralModelLabel(): string
    {
        return config('laravel-blog-engine.resources.categories.plural_label', 'Blog Categories');
    }

    public static function getNavigationLabel(): string
    {
        return config('laravel-blog-engine.resources.categories.navigation_label', 'Blog Categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('laravel-blog-engine.resources.categories.navigation_group', 'Blog');
    }

    public static function getNavigationSort(): ?int
    {
        return config('laravel-blog-engine.resources.categories.navigation_sort', 3);
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return config('laravel-blog-engine.resources.categories.navigation_icon', Heroicon::OutlinedRectangleStack);
    }

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
