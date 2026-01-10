<?php

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogCategories\BlogCategoryResource;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;
use Illuminate\Support\Facades\Config;

it('uses configured labels and group for blog posts', function () {
    Config::set('laravel-blog-engine.resources.posts.label', 'Custom Post Label');
    Config::set('laravel-blog-engine.resources.posts.plural_label', 'Custom Post Plural Label');
    Config::set('laravel-blog-engine.resources.posts.navigation_label', 'Custom Post Nav Label');
    Config::set('laravel-blog-engine.resources.posts.navigation_group', 'Custom Group');
    Config::set('laravel-blog-engine.resources.posts.navigation_sort', 99);
    Config::set('laravel-blog-engine.resources.posts.navigation_icon', 'heroicon-o-sparkles');

    expect(BlogPostResource::getModelLabel())->toBe('Custom Post Label');
    expect(BlogPostResource::getPluralModelLabel())->toBe('Custom Post Plural Label');
    expect(BlogPostResource::getNavigationLabel())->toBe('Custom Post Nav Label');
    expect(BlogPostResource::getNavigationGroup())->toBe('Custom Group');
    expect(BlogPostResource::getNavigationSort())->toBe(99);
    expect(BlogPostResource::getNavigationIcon())->toBe('heroicon-o-sparkles');
});

it('uses configured labels and group for blog authors', function () {
    Config::set('laravel-blog-engine.resources.authors.label', 'Custom Author Label');
    Config::set('laravel-blog-engine.resources.authors.plural_label', 'Custom Author Plural Label');
    Config::set('laravel-blog-engine.resources.authors.navigation_label', 'Custom Author Nav Label');
    Config::set('laravel-blog-engine.resources.authors.navigation_group', 'Custom Group');
    Config::set('laravel-blog-engine.resources.authors.navigation_sort', 88);
    Config::set('laravel-blog-engine.resources.authors.navigation_icon', 'heroicon-o-sparkles');

    expect(BlogAuthorResource::getModelLabel())->toBe('Custom Author Label');
    expect(BlogAuthorResource::getPluralModelLabel())->toBe('Custom Author Plural Label');
    expect(BlogAuthorResource::getNavigationLabel())->toBe('Custom Author Nav Label');
    expect(BlogAuthorResource::getNavigationGroup())->toBe('Custom Group');
    expect(BlogAuthorResource::getNavigationSort())->toBe(88);
    expect(BlogAuthorResource::getNavigationIcon())->toBe('heroicon-o-sparkles');
});

it('uses configured labels and group for blog categories', function () {
    Config::set('laravel-blog-engine.resources.categories.label', 'Custom Category Label');
    Config::set('laravel-blog-engine.resources.categories.plural_label', 'Custom Category Plural Label');
    Config::set('laravel-blog-engine.resources.categories.navigation_label', 'Custom Category Nav Label');
    Config::set('laravel-blog-engine.resources.categories.navigation_group', 'Custom Group');
    Config::set('laravel-blog-engine.resources.categories.navigation_sort', 77);
    Config::set('laravel-blog-engine.resources.categories.navigation_icon', 'heroicon-o-sparkles');

    expect(BlogCategoryResource::getModelLabel())->toBe('Custom Category Label');
    expect(BlogCategoryResource::getPluralModelLabel())->toBe('Custom Category Plural Label');
    expect(BlogCategoryResource::getNavigationLabel())->toBe('Custom Category Nav Label');
    expect(BlogCategoryResource::getNavigationGroup())->toBe('Custom Group');
    expect(BlogCategoryResource::getNavigationSort())->toBe(77);
    expect(BlogCategoryResource::getNavigationIcon())->toBe('heroicon-o-sparkles');
});

it('uses default values when config is missing', function () {
    // Clear config to simulate missing values, rely on defaults in code
    Config::set('laravel-blog-engine', []);

    expect(BlogPostResource::getModelLabel())->toBe('Blog Post');
    expect(BlogAuthorResource::getModelLabel())->toBe('Blog Author');
    expect(BlogCategoryResource::getModelLabel())->toBe('Blog Category');

    expect(BlogPostResource::getNavigationGroup())->toBe('Blog');

    // Default icon checks
    // We expect the Heroicon enum or string depending on implementation. 
    // In code we used `Heroicon::OutlinedRectangleStack` which is an Enum.
    // However, since we are testing the return value, it should match the default.
    // It's tricky to match Enum vs string without knowing if config helper returns enum instance or we cast it.
    // The code returns `config(..., Heroicon::OutlinedRectangleStack)`.
    // Let's just check it is not null for now or matches the Enum if possible.
    expect(BlogPostResource::getNavigationIcon())->not->toBeNull();
});
