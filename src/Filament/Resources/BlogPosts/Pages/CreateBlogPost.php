<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;
}
