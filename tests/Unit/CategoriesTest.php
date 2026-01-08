<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Http\Resources\Categories\BlogCategoryCollection;
use Joaoolival\LaravelBlogEngine\Http\Resources\Categories\BlogCategoryResource;
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

describe('Model', function () {
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
});

describe('Facade', function () {
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

        it('returns empty collection when no visible categories', function () {
            BlogCategory::factory()->hidden()->count(3)->create();

            $categories = Blog::getAllCategories();

            expect($categories)->toBeInstanceOf(Collection::class)
                ->and($categories)->toHaveCount(0);
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
});

describe('Resource', function () {
    it('transforms category to correct JSON structure', function () {
        $category = BlogCategory::factory()->create();

        $resource = new BlogCategoryResource($category);
        $json = $resource->toArray(request());

        expect($json)->toHaveKeys(['id', 'name', 'slug', 'description', 'is_visible', 'seo_title', 'seo_description', 'banner_image', 'created_at', 'updated_at']);
    });

    it('returns null for banner_image when no media', function () {
        $category = BlogCategory::factory()->create();

        $resource = new BlogCategoryResource($category);
        $json = $resource->toArray(request());

        expect($json['banner_image'])->toBeNull();
    });

    it('wraps collection correctly', function () {
        BlogCategory::factory()->count(3)->create();
        $categories = BlogCategory::all();

        $collection = new BlogCategoryCollection($categories);
        $json = $collection->toArray(request());

        expect($json)->toHaveCount(3);
    });
});
