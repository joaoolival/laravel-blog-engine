<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

beforeEach(function () {
    // Run package migrations
    foreach (glob(__DIR__.'/../../../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }

    // Run media library migration
    $mediaLibraryMigration = __DIR__.'/../../../../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
    if (File::exists($mediaLibraryMigration)) {
        (include $mediaLibraryMigration)->up();
    }
});

describe('getPublishedPosts', function () {
    it('returns all published posts when no perPage is provided', function () {
        BlogPost::factory()->published()->count(5)->create();
        BlogPost::factory()->draft()->count(2)->create();

        $posts = Blog::getPublishedPosts();

        expect($posts)->toBeInstanceOf(Collection::class)
            ->and($posts)->toHaveCount(5);
    });

    it('returns paginated posts when perPage is provided', function () {
        BlogPost::factory()->published()->count(15)->create();

        $posts = Blog::getPublishedPosts(perPage: 10);

        expect($posts)->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($posts->count())->toBe(10)
            ->and($posts->total())->toBe(15);
    });

    it('excludes draft posts', function () {
        $published = BlogPost::factory()->published()->create();
        $draft = BlogPost::factory()->draft()->create();

        $posts = Blog::getPublishedPosts();

        expect($posts->pluck('id'))->toContain($published->id)
            ->and($posts->pluck('id'))->not->toContain($draft->id);
    });

    it('excludes scheduled posts', function () {
        $published = BlogPost::factory()->published()->create();
        $scheduled = BlogPost::factory()->scheduled()->create();

        $posts = Blog::getPublishedPosts();

        expect($posts->pluck('id'))->toContain($published->id)
            ->and($posts->pluck('id'))->not->toContain($scheduled->id);
    });
});

describe('getPostBySlug', function () {
    it('returns a post by slug', function () {
        $post = BlogPost::factory()->published()->create(['slug' => 'test-post']);

        $result = Blog::getPostBySlug('test-post');

        expect($result)->toBeInstanceOf(BlogPost::class)
            ->and($result->id)->toBe($post->id);
    });

    it('throws exception for non-existent slug', function () {
        expect(fn () => Blog::getPostBySlug('non-existent'))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });

    it('throws exception for draft post', function () {
        BlogPost::factory()->draft()->create(['slug' => 'draft-post']);

        expect(fn () => Blog::getPostBySlug('draft-post'))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });

    it('eager loads author and category', function () {
        $post = BlogPost::factory()->published()->create(['slug' => 'loaded-post']);

        $result = Blog::getPostBySlug('loaded-post');

        expect($result->relationLoaded('author'))->toBeTrue()
            ->and($result->relationLoaded('category'))->toBeTrue();
    });
});
