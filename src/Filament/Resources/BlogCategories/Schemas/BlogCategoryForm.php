<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('banner_image')
                            ->disk('public')
                            ->collection('banner_image')
                            ->image()
                            ->multiple()
                            ->maxFiles(1)
                            ->maxSize(10240)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->maxLength(60)
                            ->helperText('Max 60 characters'),
                        Textarea::make('seo_description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Max 160 characters')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
