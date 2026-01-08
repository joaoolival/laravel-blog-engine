# Laravel Blog Engine

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)
[![Total Downloads](https://img.shields.io/packagist/dt/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)

A powerful blog engine for Laravel with Filament admin panel integration. Manage posts, authors, and categories with a clean Facade API.

## Requirements

-   PHP 8.3+
-   Laravel 12.0+

## Installation

```bash
composer require joaoolival/laravel-blog-engine
php artisan blog-engine:install
php artisan storage:link
```

The install command will publish migrations, set up Filament, and optionally create an admin user.

## Quick Start

```php
use Joaoolival\LaravelBlogEngine\Facades\Blog;

// Get published posts
$posts = Blog::getPublishedPosts(perPage: 12);

// Get single post
$post = Blog::getPostBySlug('my-post');

// Get authors & categories
$authors = Blog::getAllAuthors();
$categories = Blog::getAllCategories();
```

## API Usage (Inertia/Vue/React)

Use HTTP Resources for JSON transformation with optimized responsive images:

```php
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\{BlogPostResource, BlogPostCollection};

class PostController extends Controller
{
    public function index()
    {
        return Inertia::render('Blog/Index', [
            'posts' => new BlogPostCollection(Blog::getPublishedPosts(perPage: 12)),
        ]);
    }

    public function show(string $slug)
    {
        return Inertia::render('Blog/Show', [
            'post' => new BlogPostResource(Blog::getPostBySlug($slug)),
        ]);
    }
}
```

### Content Rendering Performance

**Post content is automatically cached** and optimized for performance:

1.  **Initial Save:** The API returns the raw `content` (Tiptap JSON) via a fallback mechanism, ensuring content is immediately available.
2.  **Async Optimization:** A background listener waits for Spatie Media Library to finish generating responsive images.
3.  **Final Update:** Once images are ready, the `rendered_content` column is updated with the full HTML including WebP images and `srcset` attributes.

This "eventual consistency" approach ensures fast writes while guaranteeing the best possible frontend experience for readers.

To manually regenerate immediately (e.g., during seeding):

```php
$post->regenerateRenderedContent();
```

## Available Methods

| Method                                              | Returns                 | Description            |
| --------------------------------------------------- | ----------------------- | ---------------------- |
| `getPublishedPosts(?int $perPage)`                  | `Collection\|Paginator` | Published posts        |
| `getPostBySlug(string $slug)`                       | `BlogPost`              | Single post            |
| `getAllAuthors()`                                   | `Collection`            | All visible authors    |
| `getAuthorWithPosts(string $slug, ?int $perPage)`   | `array`                 | Author with posts      |
| `getAllCategories()`                                | `Collection`            | All visible categories |
| `getCategoryWithPosts(string $slug, ?int $perPage)` | `array`                 | Category with posts    |

> When `perPage` is null, results return as Collection. Otherwise, returns Paginator.

## Models

### BlogPost

| Attribute          | Type           | Description                        |
| ------------------ | -------------- | ---------------------------------- |
| `title`            | `string`       | Post title                         |
| `slug`             | `string`       | URL slug                           |
| `excerpt`          | `string\|null` | Summary                            |
| `content`          | `string\|null` | Rich text (Tiptap JSON)            |
| `rendered_content` | `string\|null` | Cached HTML with responsive images |
| `tags`             | `array\|null`  | Tags                               |
| `is_visible`       | `bool`         | Visibility                         |
| `published_at`     | `Carbon\|null` | Publish date                       |

**Scopes:**

```php
BlogPost::whereIsPublished()->get();  // Published & visible
BlogPost::whereIsDraft()->get();       // Drafts & scheduled
BlogPost::whereIsVisible()->get();     // Visible only
```

**Media:**

```php
$post->getFirstMediaUrl('gallery', 'webp');
```

### BlogAuthor

| Attribute                                | Type           |
| ---------------------------------------- | -------------- |
| `name`, `slug`, `email`                  | `string`       |
| `bio`, `github_handle`, `twitter_handle` | `string\|null` |

### BlogCategory

| Attribute                                     | Type           |
| --------------------------------------------- | -------------- |
| `name`, `slug`                                | `string`       |
| `description`, `seo_title`, `seo_description` | `string\|null` |
| `is_visible`                                  | `bool`         |

## Admin Panel

Access Filament admin at `/admin`:

-   Posts: `/admin/blog-posts`
-   Authors: `/admin/blog-authors`
-   Categories: `/admin/blog-categories`

## Responsive Images

Uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) with automatic WebP conversion. For background processing:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Testing

```bash
composer test
```

## Credits

-   [João Olival](https://github.com/joaoolival)
-   [All Contributors](../../contributors)

## License

MIT. See [License File](LICENSE.md).
