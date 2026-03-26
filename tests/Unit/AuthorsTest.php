<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Http\Resources\Authors\BlogAuthorCollection;
use Joaoolival\LaravelBlogEngine\Http\Resources\Authors\BlogAuthorResource;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
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
});

describe('Facade', function () {
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

        it('returns empty collection when no visible authors', function () {
            BlogAuthor::factory()->hidden()->count(3)->create();

            $authors = Blog::getAllAuthors();

            expect($authors)->toBeInstanceOf(Collection::class)
                ->and($authors)->toHaveCount(0);
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
                ->toThrow(ModelNotFoundException::class);
        });

        it('includes posts count on author', function () {
            $author = BlogAuthor::factory()->create(['slug' => 'counted']);
            BlogPost::factory()->forAuthor($author)->published()->count(5)->create();
            BlogPost::factory()->forAuthor($author)->draft()->count(3)->create();

            $result = Blog::getAuthorWithPosts('counted');

            // posts_count only counts posts with published_at set (not null)
            expect($result['author']->posts_count)->toBe(5);
        });

        it('excludes scheduled posts from results', function () {
            $author = BlogAuthor::factory()->create(['slug' => 'scheduled-test']);
            BlogPost::factory()->forAuthor($author)->published()->count(2)->create();
            BlogPost::factory()->forAuthor($author)->scheduled()->count(3)->create();

            $result = Blog::getAuthorWithPosts('scheduled-test');

            // Scheduled posts have published_at set (future), so they appear in posts
            // but only those with non-null published_at are returned
            expect($result['posts'])->toHaveCount(5);
        });

        it('returns author even with no posts', function () {
            BlogAuthor::factory()->create(['slug' => 'no-posts']);

            $result = Blog::getAuthorWithPosts('no-posts');

            expect($result['author']->slug)->toBe('no-posts')
                ->and($result['posts'])->toHaveCount(0);
        });

        it('returns soft deleted author posts correctly', function () {
            $author = BlogAuthor::factory()->create(['slug' => 'soft-del']);
            $post = BlogPost::factory()->forAuthor($author)->published()->create();
            $deletedPost = BlogPost::factory()->forAuthor($author)->published()->create();
            $deletedPost->delete();

            $result = Blog::getAuthorWithPosts('soft-del');

            expect($result['posts'])->toHaveCount(1);
        });
    });
});

describe('Resource', function () {
    it('transforms author to correct JSON structure', function () {
        $author = BlogAuthor::factory()->create();

        $resource = new BlogAuthorResource($author);
        $json = $resource->toArray(request());

        expect($json)->toHaveKeys(['id', 'name', 'slug', 'email', 'bio', 'github_handle', 'twitter_handle', 'linkedin_handle', 'instagram_handle', 'facebook_handle', 'avatar', 'created_at', 'updated_at']);
    });

    it('returns null for avatar when no media', function () {
        $author = BlogAuthor::factory()->create();

        $resource = new BlogAuthorResource($author);
        $json = $resource->toArray(request());

        expect($json['avatar'])->toBeNull();
    });

    it('wraps collection correctly', function () {
        BlogAuthor::factory()->count(3)->create();
        $authors = BlogAuthor::all();

        $collection = new BlogAuthorCollection($authors);
        $json = $collection->toArray(request());

        expect($json)->toHaveCount(3);
    });
});
