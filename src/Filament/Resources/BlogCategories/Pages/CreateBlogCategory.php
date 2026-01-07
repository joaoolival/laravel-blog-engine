<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages;

use Filament\Resources\Pages\CreateRecord;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\BlogCategoryResource;

class CreateBlogCategory extends CreateRecord
{
    protected static string $resource = BlogCategoryResource::class;
}
