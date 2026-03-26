<?php

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\Actions\Posts\RenderContentAction;
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

        $action = new RenderContentAction;
        $rendered = $action->handle($post);

        expect($rendered)->not->toBeNull()
            ->and($rendered)->toContain('Test content');
    });

    it('returns null when content is null', function () {
        $post = BlogPost::factory()->create(['content' => null]);

        $action = new RenderContentAction;
        $rendered = $action->handle($post);

        expect($rendered)->toBeNull();
    });

    it('handles content with script tags in tests', function () {
        // This test verifies the try-catch in RenderContentAction works
        $post = BlogPost::factory()->create([
            'content' => '<p>Content with <script>alert("test")</script></p>',
        ]);

        $action = new RenderContentAction;
        $rendered = $action->handle($post);

        // Should return content (raw or sanitized depending on environment)
        expect($rendered)->not->toBeNull();
    });

    it('renders content with empty string as empty', function () {
        $post = BlogPost::factory()->create(['content' => '']);

        $action = new RenderContentAction;
        $rendered = $action->handle($post);

        expect($rendered)->toBeNull();
    });

    it('registers gallery and content-attachments media collections', function () {
        $post = BlogPost::factory()->create();
        $collections = collect($post->getRegisteredMediaCollections());

        expect($collections->pluck('name')->toArray())
            ->toContain('gallery')
            ->toContain('content-attachments');
    });

    it('registers avatar media collection on author as single file', function () {
        $author = BlogAuthor::factory()->create();
        $collections = collect($author->getRegisteredMediaCollections());
        $avatarCollection = $collections->firstWhere('name', 'avatar');

        expect($avatarCollection)->not->toBeNull()
            ->and($avatarCollection->singleFile)->toBeTrue();
    });

    it('registers banner_image media collection on category as single file', function () {
        $category = BlogCategory::factory()->create();
        $collections = collect($category->getRegisteredMediaCollections());
        $bannerCollection = $collections->firstWhere('name', 'banner_image');

        expect($bannerCollection)->not->toBeNull()
            ->and($bannerCollection->singleFile)->toBeTrue();
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

        it('excludes hidden posts', function () {
            BlogPost::factory()->published()->count(2)->create();
            BlogPost::factory()->published()->hidden()->count(3)->create();

            $posts = Blog::getPublishedPosts();

            expect($posts)->toHaveCount(2);
        });

        it('orders by published_at descending', function () {
            $older = BlogPost::factory()->published()->create(['published_at' => now()->subDays(5)]);
            $newer = BlogPost::factory()->published()->create(['published_at' => now()->subDay()]);

            $posts = Blog::getPublishedPosts();

            expect($posts->first()->id)->toBe($newer->id)
                ->and($posts->last()->id)->toBe($older->id);
        });

        it('excludes soft deleted posts', function () {
            $post = BlogPost::factory()->published()->create();
            BlogPost::factory()->published()->create();
            $post->delete();

            $posts = Blog::getPublishedPosts();

            expect($posts)->toHaveCount(1);
        });

        it('eager loads author and category', function () {
            BlogPost::factory()->published()->create();

            $posts = Blog::getPublishedPosts();

            expect($posts->first()->relationLoaded('author'))->toBeTrue()
                ->and($posts->first()->relationLoaded('category'))->toBeTrue();
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
                ->toThrow(ModelNotFoundException::class);
        });

        it('throws exception for draft post', function () {
            BlogPost::factory()->draft()->create(['slug' => 'draft-post']);

            expect(fn () => Blog::getPostBySlug('draft-post'))
                ->toThrow(ModelNotFoundException::class);
        });

        it('eager loads author and category', function () {
            $post = BlogPost::factory()->published()->create(['slug' => 'loaded-post']);

            $result = Blog::getPostBySlug('loaded-post');

            expect($result->relationLoaded('author'))->toBeTrue()
                ->and($result->relationLoaded('category'))->toBeTrue();
        });

        it('throws exception for hidden post', function () {
            BlogPost::factory()->published()->hidden()->create(['slug' => 'hidden-post']);

            expect(fn () => Blog::getPostBySlug('hidden-post'))
                ->toThrow(ModelNotFoundException::class);
        });

        it('throws exception for scheduled post', function () {
            BlogPost::factory()->scheduled()->create(['slug' => 'scheduled-post']);

            expect(fn () => Blog::getPostBySlug('scheduled-post'))
                ->toThrow(ModelNotFoundException::class);
        });

        it('throws exception for soft deleted post', function () {
            $post = BlogPost::factory()->published()->create(['slug' => 'deleted-post']);
            $post->delete();

            expect(fn () => Blog::getPostBySlug('deleted-post'))
                ->toThrow(ModelNotFoundException::class);
        });

        it('eager loads media', function () {
            BlogPost::factory()->published()->create(['slug' => 'media-post']);

            $result = Blog::getPostBySlug('media-post');

            expect($result->relationLoaded('media'))->toBeTrue();
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

        it('returns fewer posts when not enough exist', function () {
            BlogPost::factory()->published()->count(2)->create();

            $posts = Blog::getRecentPosts(10);

            expect($posts)->toHaveCount(2);
        });

        it('returns empty collection when no published posts exist', function () {
            BlogPost::factory()->draft()->count(3)->create();

            $posts = Blog::getRecentPosts(5);

            expect($posts)->toBeEmpty();
        });

        it('excludes draft and hidden posts', function () {
            BlogPost::factory()->published()->count(2)->create();
            BlogPost::factory()->draft()->count(2)->create();
            BlogPost::factory()->published()->hidden()->count(2)->create();

            $posts = Blog::getRecentPosts(10);

            expect($posts)->toHaveCount(2);
        });

        it('uses default limit of 5', function () {
            BlogPost::factory()->published()->count(10)->create();

            $posts = Blog::getRecentPosts();

            expect($posts)->toHaveCount(5);
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

        it('excludes the original post from results', function () {
            $category = BlogCategory::factory()->create();
            $post = BlogPost::factory()->published()->forCategory($category)->create();
            BlogPost::factory()->published()->forCategory($category)->count(2)->create();

            $result = Blog::getRelatedPosts($post, 10);

            expect($result->pluck('id'))->not->toContain($post->id);
        });

        it('respects the limit parameter', function () {
            $category = BlogCategory::factory()->create();
            $post = BlogPost::factory()->published()->forCategory($category)->create();
            BlogPost::factory()->published()->forCategory($category)->count(10)->create();

            $result = Blog::getRelatedPosts($post, 3);

            expect($result)->toHaveCount(3);
        });

        it('returns empty collection when post has no category', function () {
            $post = BlogPost::factory()->published()->create(['blog_category_id' => null]);

            $result = Blog::getRelatedPosts($post);

            expect($result)->toBeEmpty();
        });

        it('excludes draft and hidden posts from related', function () {
            $category = BlogCategory::factory()->create();
            $post = BlogPost::factory()->published()->forCategory($category)->create();
            BlogPost::factory()->draft()->forCategory($category)->count(2)->create();
            BlogPost::factory()->published()->hidden()->forCategory($category)->count(2)->create();
            $publishedRelated = BlogPost::factory()->published()->forCategory($category)->create();

            $result = Blog::getRelatedPosts($post, 10);

            expect($result)->toHaveCount(1)
                ->and($result->first()->id)->toBe($publishedRelated->id);
        });

        it('eager loads author and category', function () {
            $category = BlogCategory::factory()->create();
            $post = BlogPost::factory()->published()->forCategory($category)->create();
            BlogPost::factory()->published()->forCategory($category)->create();

            $result = Blog::getRelatedPosts($post);

            expect($result->first()->relationLoaded('author'))->toBeTrue()
                ->and($result->first()->relationLoaded('category'))->toBeTrue();
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

        it('searches by excerpt', function () {
            BlogPost::factory()->published()->create([
                'title' => 'Generic Title',
                'excerpt' => 'This post talks about Kubernetes deployment',
            ]);
            BlogPost::factory()->published()->create(['title' => 'Other Post', 'excerpt' => 'Nothing relevant']);

            $results = Blog::searchPosts('Kubernetes');

            expect($results)->toHaveCount(1);
        });

        it('searches by content', function () {
            BlogPost::factory()->published()->create([
                'title' => 'Generic Title',
                'excerpt' => 'Generic excerpt',
                'content' => '<p>Deep dive into Docker containers</p>',
            ]);
            BlogPost::factory()->published()->create(['title' => 'Other', 'content' => '<p>Unrelated</p>']);

            $results = Blog::searchPosts('Docker');

            expect($results)->toHaveCount(1);
        });

        it('is case insensitive', function () {
            BlogPost::factory()->published()->create(['title' => 'LARAVEL Best Practices']);

            $results = Blog::searchPosts('laravel');

            expect($results)->toHaveCount(1);
        });

        it('returns empty collection when no matches', function () {
            BlogPost::factory()->published()->count(3)->create();

            $results = Blog::searchPosts('nonexistentxyz');

            expect($results)->toHaveCount(0);
        });

        it('excludes draft and hidden posts from search', function () {
            BlogPost::factory()->draft()->create(['title' => 'Draft Searchable']);
            BlogPost::factory()->published()->hidden()->create(['title' => 'Hidden Searchable']);
            BlogPost::factory()->published()->create(['title' => 'Published Searchable']);

            $results = Blog::searchPosts('Searchable');

            expect($results)->toHaveCount(1)
                ->and($results->first()->title)->toBe('Published Searchable');
        });

        it('orders results by published_at descending', function () {
            BlogPost::factory()->published()->create([
                'title' => 'Older Match',
                'published_at' => now()->subDays(5),
            ]);
            BlogPost::factory()->published()->create([
                'title' => 'Newer Match',
                'published_at' => now()->subDay(),
            ]);

            $results = Blog::searchPosts('Match');

            expect($results->first()->title)->toBe('Newer Match');
        });

        it('eager loads author and category', function () {
            BlogPost::factory()->published()->create(['title' => 'Eager Loading Test']);

            $results = Blog::searchPosts('Eager');

            expect($results->first()->relationLoaded('author'))->toBeTrue()
                ->and($results->first()->relationLoaded('category'))->toBeTrue();
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
