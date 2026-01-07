<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;
}
