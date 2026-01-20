<?php

use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

beforeEach(function () {
    // Run package migrations
    foreach (glob(__DIR__.'/../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }

    // Run media library migration
    $mediaLibraryMigration = __DIR__.'/../../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
    if (File::exists($mediaLibraryMigration)) {
        (include $mediaLibraryMigration)->up();
    }
});

describe('BlogAuthorFactory', function () {
    it('creates a valid blog author', function () {
        $author = BlogAuthor::factory()->create();

        expect($author)->toBeInstanceOf(BlogAuthor::class)
            ->and($author->name)->not->toBeEmpty()
            ->and($author->slug)->not->toBeEmpty()
            ->and($author->email)->not->toBeEmpty()
            ->and($author->is_visible)->toBeTrue();
    });

    it('creates a hidden author with hidden state', function () {
        $author = BlogAuthor::factory()->hidden()->create();

        expect($author->is_visible)->toBeFalse();
    });
});

describe('BlogCategoryFactory', function () {
    it('creates a valid blog category', function () {
        $category = BlogCategory::factory()->create();

        expect($category)->toBeInstanceOf(BlogCategory::class)
            ->and($category->name)->not->toBeEmpty()
            ->and($category->slug)->not->toBeEmpty()
            ->and($category->is_visible)->toBeTrue();
    });

    it('creates a hidden category with hidden state', function () {
        $category = BlogCategory::factory()->hidden()->create();

        expect($category->is_visible)->toBeFalse();
    });
});

describe('BlogPostFactory', function () {
    it('creates a valid blog post with relationships', function () {
        $post = BlogPost::factory()->create();

        expect($post)->toBeInstanceOf(BlogPost::class)
            ->and($post->title)->not->toBeEmpty()
            ->and($post->slug)->not->toBeEmpty()
            ->and($post->blog_author_id)->not->toBeNull()
            ->and($post->blog_category_id)->not->toBeNull()
            ->and($post->author)->toBeInstanceOf(BlogAuthor::class)
            ->and($post->category)->toBeInstanceOf(BlogCategory::class);
    });

    it('creates a draft post with draft state', function () {
        $post = BlogPost::factory()->draft()->create();

        expect($post->published_at)->toBeNull();
    });

    it('creates a scheduled post with scheduled state', function () {
        $post = BlogPost::factory()->scheduled()->create();

        expect($post->published_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($post->published_at->isFuture())->toBeTrue();
    });

    it('creates a published post with published state', function () {
        $post = BlogPost::factory()->published()->create();

        expect($post->published_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
            ->and($post->published_at->isPast())->toBeTrue()
            ->and($post->is_visible)->toBeTrue();
    });

    it('creates a hidden post with hidden state', function () {
        $post = BlogPost::factory()->hidden()->create();

        expect($post->is_visible)->toBeFalse();
    });

    it('creates a post for a specific author', function () {
        $author = BlogAuthor::factory()->create();
        $post = BlogPost::factory()->forAuthor($author)->create();

        expect($post->blog_author_id)->toBe($author->id);
    });

    it('creates a post for a specific category', function () {
        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->forCategory($category)->create();

        expect($post->blog_category_id)->toBe($category->id);
    });
});
