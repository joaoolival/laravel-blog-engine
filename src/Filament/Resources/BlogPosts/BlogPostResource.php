<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Schemas\BlogPostForm;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Tables\BlogPostsTable;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $recordTitleAttribute = 'title';

    #[\Override]
    public static function getModelLabel(): string
    {
        return config('laravel-blog-engine.resources.posts.label', 'Blog Post');
    }

    #[\Override]
    public static function getPluralModelLabel(): string
    {
        return config('laravel-blog-engine.resources.posts.plural_label', 'Blog Posts');
    }

    #[\Override]
    public static function getNavigationLabel(): string
    {
        return config('laravel-blog-engine.resources.posts.navigation_label', 'Blog Posts');
    }

    #[\Override]
    public static function getNavigationGroup(): ?string
    {
        return config('laravel-blog-engine.resources.posts.navigation_group', 'Blog');
    }

    #[\Override]
    public static function getNavigationSort(): ?int
    {
        return config('laravel-blog-engine.resources.posts.navigation_sort', 1);
    }

    #[\Override]
    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return config('laravel-blog-engine.resources.posts.navigation_icon', Heroicon::OutlinedRectangleStack);
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return BlogPostForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return BlogPostsTable::configure($table);
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
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }

    /**
     * @return Builder<BlogPost>
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
