<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\CreateBlogAuthor;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\EditBlogAuthor;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\ListBlogAuthors;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Schemas\BlogAuthorForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Tables\BlogAuthorsTable;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;

class BlogAuthorResource extends Resource
{
    protected static ?string $model = BlogAuthor::class;

    protected static ?string $recordTitleAttribute = 'name';

    #[\Override]
    public static function getModelLabel(): string
    {
        return config('laravel-blog-engine.resources.authors.label', 'Blog Author');
    }

    #[\Override]
    public static function getPluralModelLabel(): string
    {
        return config('laravel-blog-engine.resources.authors.plural_label', 'Blog Authors');
    }

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return config('laravel-blog-engine.resources.authors.navigation_label', 'Blog Authors');
    }

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return config('laravel-blog-engine.resources.authors.navigation_group', 'Blog');
    }

    #[\Override]
    public static function getNavigationSort(): ?int
    {
        return config('laravel-blog-engine.resources.authors.navigation_sort', 2);
    }

    #[\Override]
    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return config('laravel-blog-engine.resources.authors.navigation_icon', Heroicon::OutlinedRectangleStack);
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return BlogAuthorForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return BlogAuthorsTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListBlogAuthors::route('/'),
            'create' => CreateBlogAuthor::route('/create'),
            'edit' => EditBlogAuthor::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<BlogAuthor>
     */
    #[\Override]
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
