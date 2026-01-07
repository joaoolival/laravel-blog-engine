<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogAuthor extends CreateRecord
{
    protected static string $resource = BlogAuthorResource::class;
}
