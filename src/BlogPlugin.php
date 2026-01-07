<?php

namespace Joaoolival\LaravelBlogEngine;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\BlogCategoryResource;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;

class BlogPlugin implements Plugin
{
    public function getId(): string
    {
        return 'laravel-blog-engine';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                BlogAuthorResource::class,
                BlogCategoryResource::class,
                BlogPostResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return new static();
    }
}
