# Laravel Blog Engine

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)
[![Total Downloads](https://img.shields.io/packagist/dt/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)

A powerful blog engine for Laravel with Filament admin panel integration. Manage posts, authors, and categories with a beautiful admin interface, and query your content using a clean Facade API.

## Installation

You can install the package via composer:

```bash
composer require joaoolival/laravel-blog-engine
```

Then run the install command:

```bash
php artisan blog-engine:install
```

This will publish and run migrations, set up Filament, and optionally create an admin user.

Finally, create the storage symbolic link for images:

```bash
php artisan storage:link
```

## Basic Usage

The package provides a `Blog` facade for querying your content:

```php
use Joaoolival\LaravelBlogEngine\Facades\Blog;

// Get all published posts
$posts = Blog::getPublishedPosts();

// Get paginated published posts
$posts = Blog::getPublishedPosts(perPage: 12);

// Get a single post by slug
$post = Blog::getPostBySlug('my-first-post');

// Get all visible authors
$authors = Blog::getAllAuthors();

// Get all visible categories
$categories = Blog::getAllCategories();

// Get author with all their posts
$data = Blog::getAuthorWithPosts('john-doe');
// $data['author'], $data['posts']

// Get author with paginated posts
$data = Blog::getAuthorWithPosts('john-doe', perPage: 12);
```

## Using with Inertia/API

The package includes API Resources for JSON transformation:

```php
use Joaoolival\LaravelBlogEngine\Facades\Blog;
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostCollection;
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostResource;

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

## Facade Methods

| Method                                                     | Returns                 | Description                                     |
| ---------------------------------------------------------- | ----------------------- | ----------------------------------------------- |
| `getPublishedPosts(?int $perPage = null)`                  | `Paginator\|Collection` | Published posts (paginated if perPage provided) |
| `getPostBySlug(string $slug)`                              | `BlogPost`              | Single post by slug                             |
| `getAllAuthors()`                                          | `Collection`            | All visible authors                             |
| `getAuthorWithPosts(string $slug, ?int $perPage = null)`   | `array`                 | Author + posts                                  |
| `getAllCategories()`                                       | `Collection`            | All visible categories                          |
| `getCategoryWithPosts(string $slug, ?int $perPage = null)` | `array`                 | Category + posts                                |

> **Note:** When `perPage` is `null`, all results are returned as a Collection. When `perPage` is provided, results are paginated.

## Using Actions

For dependency injection or testing, use Actions directly:

```php
use Joaoolival\LaravelBlogEngine\Actions\Posts\GetPublishedPostsAction;

class PostController extends Controller
{
    public function index(GetPublishedPostsAction $action)
    {
        $posts = $action->handle(perPage: 12);
        // ...
    }
}
```

Available Actions:

-   `Actions\Posts\GetPublishedPostsAction`
-   `Actions\Posts\GetPostBySlugAction`
-   `Actions\Authors\GetAllAuthorsAction`
-   `Actions\Authors\GetAuthorWithPostsAction`
-   `Actions\Categories\GetAllCategoriesAction`
-   `Actions\Categories\GetCategoryWithPostsAction`

## API Resources

Resources automatically handle responsive images and WebP conversion:

```php
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostResource;
use Joaoolival\LaravelBlogEngine\Http\Resources\Posts\BlogPostCollection;
use Joaoolival\LaravelBlogEngine\Http\Resources\Authors\BlogAuthorResource;
use Joaoolival\LaravelBlogEngine\Http\Resources\Categories\BlogCategoryResource;
```

The `BlogPostResource` includes:

-   Rendered content with resolved image URLs
-   WebP images with srcset for responsive loading
-   Banner image and gallery
-   Nested author and category data

## Models

### BlogPost

```php
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

// Scopes
BlogPost::whereIsPublished()->get();  // Published and visible
BlogPost::whereIsDraft()->get();       // Not yet published
BlogPost::whereIsVisible()->get();     // Visible posts

// Relationships
$post->author;    // BlogAuthor
$post->category;  // BlogCategory

// Media
$post->getFirstMediaUrl('gallery', 'webp');
```

| Attribute      | Type           | Description        |
| -------------- | -------------- | ------------------ |
| `title`        | `string`       | Post title         |
| `slug`         | `string`       | URL-friendly slug  |
| `excerpt`      | `string\|null` | Short summary      |
| `content`      | `string\|null` | Rich text content  |
| `tags`         | `array\|null`  | JSON array of tags |
| `is_visible`   | `bool`         | Visibility status  |
| `published_at` | `Carbon\|null` | Publish date       |

### BlogAuthor

```php
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;

$author->posts;  // HasMany BlogPost
$author->getFirstMediaUrl('avatar', 'webp');
```

| Attribute        | Type           | Description       |
| ---------------- | -------------- | ----------------- |
| `name`           | `string`       | Author name       |
| `slug`           | `string`       | URL-friendly slug |
| `email`          | `string`       | Author email      |
| `bio`            | `string\|null` | Biography         |
| `github_handle`  | `string\|null` | GitHub username   |
| `twitter_handle` | `string\|null` | Twitter username  |

### BlogCategory

```php
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;

$category->posts;  // HasMany BlogPost
$category->getFirstMediaUrl('banner_image', 'webp');
```

| Attribute         | Type           | Description          |
| ----------------- | -------------- | -------------------- |
| `name`            | `string`       | Category name        |
| `slug`            | `string`       | URL-friendly slug    |
| `description`     | `string\|null` | Description          |
| `is_visible`      | `bool`         | Visibility status    |
| `seo_title`       | `string\|null` | SEO meta title       |
| `seo_description` | `string\|null` | SEO meta description |

## Admin Panel

After installation, access the Filament admin at `/admin`:

| Resource   | URL                      |
| ---------- | ------------------------ |
| Posts      | `/admin/blog-posts`      |
| Authors    | `/admin/blog-authors`    |
| Categories | `/admin/blog-categories` |

To manually register the plugin:

```php
use Joaoolival\LaravelBlogEngine\BlogPlugin;

public function panel(Panel $panel): Panel
{
    return $panel->plugin(BlogPlugin::make());
}
```

## Responsive Images

This package uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for automatic image optimization. With [Laravel Horizon](https://laravel.com/docs/horizon), responsive variants are generated in the background:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Configuration

Publish the configuration:

```bash
php artisan vendor:publish --tag="laravel-blog-engine-config"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [João Olival](https://github.com/joaoolival)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
