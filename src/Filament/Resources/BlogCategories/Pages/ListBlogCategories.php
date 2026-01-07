<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\BlogCategoryResource;

class ListBlogCategories extends ListRecords
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
