<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                self::detailsSection(),
                self::contentSection(),
                self::imagesSection(),
            ]);
    }

    private static function detailsSection(): Section
    {
        return Section::make('Post Details')
            ->columnSpanFull()
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Select::make('blog_author_id')
                    ->label('Author')
                    ->relationship('author', 'name', fn (Builder $query) => $query->where('is_visible', true))
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('blog_category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn (Builder $query) => $query->where('is_visible', true))
                    ->searchable()
                    ->preload()
                    ->required(),

                DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->columnSpan(1),

                Toggle::make('is_visible')
                    ->label('Visible')
                    ->default(true)
                    ->inline(false)
                    ->columnSpan(1),

                TagsInput::make('tags')
                    ->placeholder('Add a tag')
                    ->reorderable()
                    ->columnSpanFull(),

            ])
            ->columns(2);
    }

    private static function contentSection(): Section
    {
        return Section::make('Content')
            ->columnSpanFull()
            ->schema([
                Textarea::make('excerpt')
                    ->rows(2)
                    ->required()
                    ->maxLength(500)
                    ->columnSpanFull(),

                // File attachments are handled by the model's setUpRichContent()
                // using SpatieMediaLibraryFileAttachmentProvider
                RichEditor::make('content')
                    ->required()
                    ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])
                    ->fileAttachmentsMaxSize(10240) // 10MB
                    ->columnSpanFull(),
            ]);
    }

    private static function imagesSection(): Section
    {
        return Section::make('Images')
            ->columnSpanFull()
            ->description('The first image will be used as the banner image.')
            ->schema([
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->collection('gallery')
                    ->disk('public')
                    ->image()
                    ->multiple()
                    ->maxSize(10240)
                    ->reorderable()
                    ->maxFiles(10)
                    ->columnSpanFull(),
            ]);
    }
}
