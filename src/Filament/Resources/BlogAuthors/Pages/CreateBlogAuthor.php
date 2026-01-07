<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages;

use Filament\Resources\Pages\CreateRecord;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;

class CreateBlogAuthor extends CreateRecord
{
    protected static string $resource = BlogAuthorResource::class;
}
