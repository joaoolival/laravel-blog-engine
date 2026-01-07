<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\CreateBlogAuthor;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\EditBlogAuthor;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages\ListBlogAuthors;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Schemas\BlogAuthorForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Tables\BlogAuthorsTable;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogAuthorResource extends Resource
{
    protected static ?string $model = BlogAuthor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BlogAuthorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogAuthorsTable::configure($table);
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
            'index' => ListBlogAuthors::route('/'),
            'create' => CreateBlogAuthor::route('/create'),
            'edit' => EditBlogAuthor::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<BlogAuthor>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
