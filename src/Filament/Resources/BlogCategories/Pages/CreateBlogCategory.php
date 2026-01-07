<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\Pages;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\BlogCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogCategory extends CreateRecord
{
    protected static string $resource = BlogCategoryResource::class;
}
