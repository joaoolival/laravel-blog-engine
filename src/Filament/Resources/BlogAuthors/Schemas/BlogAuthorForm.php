<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogAuthorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->collection('avatar')
                            ->disk('public')
                            ->image()
                            ->multiple()
                            ->maxFiles(1)
                            ->maxSize(10240)
                            ->imagePreviewHeight('200')
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Textarea::make('bio')
                            ->rows(4)
                            ->columnSpanFull(),
                        Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Social Links')
                    ->schema([
                        TextInput::make('github_handle')
                            ->prefix('github.com/')
                            ->maxLength(255),
                        TextInput::make('twitter_handle')
                            ->prefix('x.com/')
                            ->maxLength(255),
                        TextInput::make('linkedin_handle')
                            ->prefix('linkedin.com/in/')
                            ->maxLength(255),
                        TextInput::make('instagram_handle')
                            ->prefix('instagram.com/')
                            ->maxLength(255),
                        TextInput::make('facebook_handle')
                            ->prefix('facebook.com/')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
