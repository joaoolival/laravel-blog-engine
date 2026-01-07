<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;

class ListBlogPosts extends ListRecords
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
