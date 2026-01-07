<?php

use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

beforeEach(function () {
    // Run migrations
    foreach (glob(__DIR__.'/../../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }
});

it('can be created using factory', function () {
    $author = BlogAuthor::factory()->create();

    expect($author)->toBeInstanceOf(BlogAuthor::class)
        ->and($author->name)->toBeString()
        ->and($author->slug)->toBeString()
        ->and($author->email)->toBeString();
});

it('has posts relationship', function () {
    $author = BlogAuthor::factory()->create();
    BlogPost::factory()->forAuthor($author)->count(3)->create();

    expect($author->posts)->toHaveCount(3)
        ->and($author->posts->first())->toBeInstanceOf(BlogPost::class);
});

it('can scope to visible only', function () {
    BlogAuthor::factory()->count(2)->create(['is_visible' => true]);
    BlogAuthor::factory()->hidden()->count(3)->create();

    $visibleAuthors = BlogAuthor::whereIsVisible()->get();

    expect($visibleAuthors)->toHaveCount(2);
});

it('uses soft deletes', function () {
    $author = BlogAuthor::factory()->create();
    $authorId = $author->id;

    $author->delete();

    expect(BlogAuthor::find($authorId))->toBeNull()
        ->and(BlogAuthor::withTrashed()->find($authorId))->not->toBeNull();

    $author->restore();

    expect(BlogAuthor::find($authorId))->not->toBeNull();
});

it('casts is_visible to boolean', function () {
    $author = BlogAuthor::factory()->create(['is_visible' => 1]);

    expect($author->is_visible)->toBeBool()
        ->and($author->is_visible)->toBeTrue();
});
