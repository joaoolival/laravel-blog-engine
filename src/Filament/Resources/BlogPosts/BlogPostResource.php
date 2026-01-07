<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Schemas\BlogPostForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Tables\BlogPostsTable;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return BlogPostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BlogPostsTable::configure($table);
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
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<BlogPost>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
