<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
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

describe('getAllCategories', function () {
    it('returns all visible categories', function () {
        BlogCategory::factory()->count(4)->create(['is_visible' => true]);
        BlogCategory::factory()->hidden()->count(2)->create();

        $categories = Blog::getAllCategories();

        expect($categories)->toBeInstanceOf(Collection::class)
            ->and($categories)->toHaveCount(4);
    });

    it('orders categories by name', function () {
        BlogCategory::factory()->create(['name' => 'Technology', 'is_visible' => true]);
        BlogCategory::factory()->create(['name' => 'Art', 'is_visible' => true]);
        BlogCategory::factory()->create(['name' => 'Science', 'is_visible' => true]);

        $categories = Blog::getAllCategories();

        expect($categories->first()->name)->toBe('Art')
            ->and($categories->last()->name)->toBe('Technology');
    });
});

describe('getCategoryWithPosts', function () {
    it('returns category with its posts', function () {
        $category = BlogCategory::factory()->create(['slug' => 'tech', 'is_visible' => true]);
        BlogPost::factory()->forCategory($category)->published()->count(4)->create();

        $result = Blog::getCategoryWithPosts('tech');

        expect($result)->toBeArray()
            ->and($result['category'])->toBeInstanceOf(BlogCategory::class)
            ->and($result['category']->id)->toBe($category->id)
            ->and($result['posts'])->toHaveCount(4);
    });

    it('returns paginated posts when perPage is provided', function () {
        $category = BlogCategory::factory()->create(['slug' => 'science', 'is_visible' => true]);
        BlogPost::factory()->forCategory($category)->published()->count(20)->create();

        $result = Blog::getCategoryWithPosts('science', perPage: 10);

        expect($result['posts'])->toBeInstanceOf(LengthAwarePaginator::class)
            ->and($result['posts']->count())->toBe(10)
            ->and($result['posts']->total())->toBe(20);
    });

    it('only includes published posts', function () {
        $category = BlogCategory::factory()->create(['slug' => 'category-test', 'is_visible' => true]);
        BlogPost::factory()->forCategory($category)->published()->count(3)->create();
        BlogPost::factory()->forCategory($category)->draft()->count(2)->create();

        $result = Blog::getCategoryWithPosts('category-test');

        expect($result['posts'])->toHaveCount(3);
    });

    it('throws exception for non-existent category', function () {
        expect(fn () => Blog::getCategoryWithPosts('non-existent'))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });

    it('throws exception for hidden category', function () {
        BlogCategory::factory()->hidden()->create(['slug' => 'hidden-cat']);

        expect(fn () => Blog::getCategoryWithPosts('hidden-cat'))
            ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    });
});
