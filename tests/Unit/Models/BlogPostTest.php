<?php

use Illuminate\Support\Carbon;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

beforeEach(function () {
    // Run migrations
    foreach (glob(__DIR__.'/../../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }
});

it('can be created using factory', function () {
    $post = BlogPost::factory()->create();

    expect($post)->toBeInstanceOf(BlogPost::class)
        ->and($post->title)->toBeString()
        ->and($post->slug)->toBeString();
});

it('belongs to an author', function () {
    $author = BlogAuthor::factory()->create();
    $post = BlogPost::factory()->forAuthor($author)->create();

    expect($post->author)->toBeInstanceOf(BlogAuthor::class)
        ->and($post->author->id)->toBe($author->id);
});

it('belongs to a category', function () {
    $category = BlogCategory::factory()->create();
    $post = BlogPost::factory()->forCategory($category)->create();

    expect($post->category)->toBeInstanceOf(BlogCategory::class)
        ->and($post->category->id)->toBe($category->id);
});

it('can scope to visible only', function () {
    BlogPost::factory()->count(2)->create(['is_visible' => true]);
    BlogPost::factory()->hidden()->count(3)->create();

    $visiblePosts = BlogPost::whereIsVisible()->get();

    expect($visiblePosts)->toHaveCount(2);
});

it('can scope to drafts', function () {
    // Published posts (not drafts)
    BlogPost::factory()->published()->count(2)->create();
    // Draft posts (null published_at)
    BlogPost::factory()->draft()->count(3)->create();
    // Scheduled posts (future published_at - also drafts)
    BlogPost::factory()->scheduled()->count(1)->create();

    $draftPosts = BlogPost::whereIsDraft()->get();

    // Drafts include null published_at and future published_at
    expect($draftPosts)->toHaveCount(4);
});

it('can scope to published', function () {
    // Published and visible
    BlogPost::factory()->published()->count(2)->create();
    // Draft
    BlogPost::factory()->draft()->count(3)->create();
    // Published but hidden
    BlogPost::factory()->published()->hidden()->count(1)->create();

    $publishedPosts = BlogPost::whereIsPublished()->get();

    // Only published AND visible posts
    expect($publishedPosts)->toHaveCount(2);
});

it('treats future published_at as draft', function () {
    $scheduledPost = BlogPost::factory()->scheduled()->create();

    $draftPosts = BlogPost::whereIsDraft()->get();
    $publishedPosts = BlogPost::whereIsPublished()->get();

    expect($draftPosts->contains('id', $scheduledPost->id))->toBeTrue()
        ->and($publishedPosts->contains('id', $scheduledPost->id))->toBeFalse();
});

it('uses soft deletes', function () {
    $post = BlogPost::factory()->create();
    $postId = $post->id;

    $post->delete();

    expect(BlogPost::find($postId))->toBeNull()
        ->and(BlogPost::withTrashed()->find($postId))->not->toBeNull();

    $post->restore();

    expect(BlogPost::find($postId))->not->toBeNull();
});

it('casts tags to array', function () {
    $tags = ['php', 'laravel', 'filament'];
    $post = BlogPost::factory()->create(['tags' => $tags]);

    expect($post->tags)->toBeArray()
        ->and($post->tags)->toBe($tags);
});

it('casts published_at to datetime', function () {
    $post = BlogPost::factory()->published()->create();

    expect($post->published_at)->toBeInstanceOf(Carbon::class);
});
