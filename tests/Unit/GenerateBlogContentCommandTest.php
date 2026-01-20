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

describe('blog:generate command', function () {
    it('creates default number of records when no options provided', function () {
        $this->artisan('blog:generate', [
            '--authors' => 1,
            '--categories' => 1,
            '--posts' => 12,
        ])
            ->assertSuccessful();

        expect(BlogAuthor::count())->toBe(1)
            ->and(BlogCategory::count())->toBe(1)
            ->and(BlogPost::count())->toBe(12);
    });

    it('creates specified number of authors', function () {
        $this->artisan('blog:generate', [
            '--authors' => 5,
            '--categories' => 0,
            '--posts' => 0,
        ])
            ->assertSuccessful();

        expect(BlogAuthor::count())->toBe(5);
    });

    it('creates specified number of categories', function () {
        $this->artisan('blog:generate', [
            '--authors' => 0,
            '--categories' => 3,
            '--posts' => 0,
        ])
            ->assertSuccessful();

        expect(BlogCategory::count())->toBe(3);
    });

    it('creates specified number of posts', function () {
        $this->artisan('blog:generate', [
            '--authors' => 1,
            '--categories' => 1,
            '--posts' => 5,
        ])
            ->assertSuccessful();

        expect(BlogPost::count())->toBe(5);
    });

    it('handles zero values gracefully', function () {
        $this->artisan('blog:generate', [
            '--authors' => 0,
            '--categories' => 0,
            '--posts' => 0,
        ])
            ->assertSuccessful();

        expect(BlogAuthor::count())->toBe(0)
            ->and(BlogCategory::count())->toBe(0)
            ->and(BlogPost::count())->toBe(0);
    });

    it('creates authors and categories when posts requested but none exist', function () {
        $this->artisan('blog:generate', [
            '--authors' => 0,
            '--categories' => 0,
            '--posts' => 3,
        ])
            ->assertSuccessful();

        // Should create 1 author and 1 category automatically for posts
        expect(BlogAuthor::count())->toBe(1)
            ->and(BlogCategory::count())->toBe(1)
            ->and(BlogPost::count())->toBe(3);
    });
});
