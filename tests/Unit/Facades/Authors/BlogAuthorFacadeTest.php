<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
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

describe('getAllAuthors', function () {
    it('returns all visible authors', function () {
        BlogAuthor::factory()->count(3)->create(['is_visible' => true]);
        BlogAuthor::factory()->hidden()->count(2)->create();

        $authors = Blog::getAllAuthors();

        expect($authors)->toBeInstanceOf(Collection::class)
            ->and($authors)->toHaveCount(3);
    });

    it('orders authors by name', function () {
        BlogAuthor::factory()->create(['name' => 'Zoe', 'is_visible' => true]);
        BlogAuthor::factory()->create(['name' => 'Alice', 'is_visible' => true]);
        BlogAuthor::factory()->create(['name' => 'Bob', 'is_visible' => true]);

        $authors = Blog::getAllAuthors();

        expect($authors->first()->name)->toBe('Alice')
            ->and($authors->last()->name)->toBe('Zoe');
    });
});

describe('getAuthorWithPosts', function () {
    it('returns author with their posts', function () {
        $author = BlogAuthor::factory()->create(['slug' => 'john-doe']);
        BlogPost::factory()->forAuthor($author)->published()->count(3)->create();

        $result = Blog::getAuthorWithPosts('john-doe');

        expect($result)->toBeArray()
            ->and($result['author'])->toBeInstanceOf(BlogAuthor::class)
            ->and($result['author']->id)->toBe($author->id)
            ->and($result['posts'])->toHaveCount(3);
    });

    it('returns paginated posts when perPage is provided', function () {
        $author = BlogAuthor::factory()->create(['slug' => 'jane-doe']);
        BlogPost::factory()->forAuthor($author)->published()->count(15)->create();

        $result = Blog::getAuthorWithPosts('jane-doe', perPage: 10);

        expect($result['posts'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($result['posts']->count())->toBe(10)
            ->and($result['posts']->total())->toBe(15);
    });

    it('only includes published posts', function () {
        $author = BlogAuthor::factory()->create(['slug' => 'author-test']);
        BlogPost::factory()->forAuthor($author)->published()->count(2)->create();
        BlogPost::factory()->forAuthor($author)->draft()->count(3)->create();

        $result = Blog::getAuthorWithPosts('author-test');

        expect($result['posts'])->toHaveCount(2);
    });

    it('throws exception for non-existent author', function () {
        expect(fn () => Blog::getAuthorWithPosts('non-existent'))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });
});
