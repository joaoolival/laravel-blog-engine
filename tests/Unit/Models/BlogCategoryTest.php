<?php

use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

beforeEach(function () {
    // Run migrations
    foreach (glob(__DIR__.'/../../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }
});

it('can be created using factory', function () {
    $category = BlogCategory::factory()->create();

    expect($category)->toBeInstanceOf(BlogCategory::class)
        ->and($category->name)->toBeString()
        ->and($category->slug)->toBeString();
});

it('has posts relationship', function () {
    $category = BlogCategory::factory()->create();
    BlogPost::factory()->forCategory($category)->count(3)->create();

    expect($category->posts)->toHaveCount(3)
        ->and($category->posts->first())->toBeInstanceOf(BlogPost::class);
});

it('can scope to visible only', function () {
    BlogCategory::factory()->count(2)->create(['is_visible' => true]);
    BlogCategory::factory()->hidden()->count(3)->create();

    $visibleCategories = BlogCategory::whereIsVisible()->get();

    expect($visibleCategories)->toHaveCount(2);
});

it('uses soft deletes', function () {
    $category = BlogCategory::factory()->create();
    $categoryId = $category->id;

    $category->delete();

    expect(BlogCategory::find($categoryId))->toBeNull()
        ->and(BlogCategory::withTrashed()->find($categoryId))->not->toBeNull();

    $category->restore();

    expect(BlogCategory::find($categoryId))->not->toBeNull();
});

it('casts is_visible to boolean', function () {
    $category = BlogCategory::factory()->create(['is_visible' => 1]);

    expect($category->is_visible)->toBeBool()
        ->and($category->is_visible)->toBeTrue();
});
