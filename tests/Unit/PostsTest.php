<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostCollection;
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostResource;
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

describe('Model', function () {
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
        BlogPost::factory()->published()->count(2)->create();
        BlogPost::factory()->draft()->count(3)->create();
        BlogPost::factory()->scheduled()->count(1)->create();

        $draftPosts = BlogPost::whereIsDraft()->get();

        expect($draftPosts)->toHaveCount(4);
    });

    it('can scope to published', function () {
        BlogPost::factory()->published()->count(2)->create();
        BlogPost::factory()->draft()->count(3)->create();
        BlogPost::factory()->published()->hidden()->count(1)->create();

        $publishedPosts = BlogPost::whereIsPublished()->get();

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

    it('can render content with RenderContentAction', function () {
        $post = BlogPost::factory()->create([
            'content' => '<p>Test content</p>',
        ]);

        $action = new \Joaoolival\LaravelBlogEngine\Actions\Posts\RenderContentAction;
        $rendered = $action->handle($post);

        expect($rendered)->not->toBeNull()
            ->and($rendered)->toContain('Test content');
    });

    it('returns null when content is null', function () {
        $post = BlogPost::factory()->create(['content' => null]);

        $action = new \Joaoolival\LaravelBlogEngine\Actions\Posts\RenderContentAction;
        $rendered = $action->handle($post);

        expect($rendered)->toBeNull();
    });

    it('handles content with script tags in tests', function () {
        // This test verifies the try-catch in RenderContentAction works
        $post = BlogPost::factory()->create([
            'content' => '<p>Content with <script>alert("test")</script></p>',
        ]);

        $action = new \Joaoolival\LaravelBlogEngine\Actions\Posts\RenderContentAction;
        $rendered = $action->handle($post);

        // Should return content (raw or sanitized depending on environment)
        expect($rendered)->not->toBeNull();
    });
});

describe('Facade', function () {
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

        it('returns empty collection when no published posts', function () {
            BlogPost::factory()->draft()->count(3)->create();

            $posts = Blog::getPublishedPosts();

            expect($posts)->toBeInstanceOf(Collection::class)
                ->and($posts)->toHaveCount(0);
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

    describe('getRecentPosts', function () {
        it('returns most recent published posts', function () {
            BlogPost::factory()->published()->count(10)->create();

            $posts = Blog::getRecentPosts(5);

            expect($posts)->toBeInstanceOf(Collection::class)
                ->and($posts)->toHaveCount(5);
        });

        it('orders by published_at descending', function () {
            $older = BlogPost::factory()->published()->create(['published_at' => now()->subDays(5)]);
            $newer = BlogPost::factory()->published()->create(['published_at' => now()->subDay()]);

            $posts = Blog::getRecentPosts(2);

            expect($posts->first()->id)->toBe($newer->id);
        });
    });

    describe('getRelatedPosts', function () {
        it('returns posts from same category', function () {
            $category = BlogCategory::factory()->create();
            $post = BlogPost::factory()->published()->forCategory($category)->create();
            $related = BlogPost::factory()->published()->forCategory($category)->count(3)->create();
            $unrelated = BlogPost::factory()->published()->create();

            $result = Blog::getRelatedPosts($post, 4);

            expect($result->pluck('id'))->toContain($related->first()->id)
                ->and($result->pluck('id'))->not->toContain($unrelated->id)
                ->and($result->pluck('id'))->not->toContain($post->id);
        });
    });

    describe('searchPosts', function () {
        it('searches by title', function () {
            BlogPost::factory()->published()->create(['title' => 'Laravel Tutorial']);
            BlogPost::factory()->published()->create(['title' => 'Vue.js Guide']);

            $results = Blog::searchPosts('Laravel');

            expect($results)->toHaveCount(1)
                ->and($results->first()->title)->toBe('Laravel Tutorial');
        });

        it('returns paginated results when perPage is provided', function () {
            BlogPost::factory()->published()->count(15)->create(['title' => 'Test Post']);

            $results = Blog::searchPosts('Test', perPage: 10);

            expect($results)->toBeInstanceOf(LengthAwarePaginator::class)
                ->and($results->count())->toBe(10);
        });
    });
});

describe('Resource', function () {
    it('transforms post to correct JSON structure', function () {
        // Create post without content to avoid triggering rich content renderer
        $post = BlogPost::factory()->published()->create(['content' => null]);
        $post->load(['author', 'category']);

        $resource = new BlogPostResource($post);
        $json = $resource->toArray(request());

        expect($json)->toHaveKeys(['id', 'title', 'slug', 'excerpt', 'content', 'published_at', 'author', 'category', 'banner_image', 'gallery', 'created_at', 'updated_at']);
    });

    it('returns null for banner_image when no media', function () {
        $post = BlogPost::factory()->published()->create(['content' => null]);

        $resource = new BlogPostResource($post);
        $json = $resource->toArray(request());

        expect($json['banner_image'])->toBeNull();
    });

    it('wraps collection correctly', function () {
        BlogPost::factory()->published()->count(3)->create();
        $posts = BlogPost::all();

        $collection = new BlogPostCollection($posts);
        $json = $collection->toArray(request());

        expect($json)->toHaveCount(3);
    });
});
