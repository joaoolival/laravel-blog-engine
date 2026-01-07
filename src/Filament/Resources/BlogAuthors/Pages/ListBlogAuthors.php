<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlogAuthors extends ListRecords
{
    protected static string $resource = BlogAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
