# Laravel Blog Engine

[![Latest Version on Packagist](https://img.shields.io/packagist/v/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)
[![Total Downloads](https://img.shields.io/packagist/dt/joaoolival/laravel-blog-engine.svg?style=flat-square)](https://packagist.org/packages/joaoolival/laravel-blog-engine)

A powerful, feature-rich blog engine for Laravel applications with a beautiful Filament admin panel. Manage posts, authors, and categories with ease.

## Features

-   📝 **Full Blog Management** - Create, edit, and publish blog posts with a rich text editor
-   👥 **Author Profiles** - Manage multiple authors with social media links and bios
-   🏷️ **Categories** - Organize posts with categories
-   🖼️ **Media Management** - Built-in image handling with Spatie Media Library
-   📱 **Responsive Images** - Automatic image optimization for better SEO and performance
-   🎨 **Filament Integration** - Beautiful admin interface powered by Filament

## Requirements

-   PHP 8.4+
-   Laravel 11.x or 12.x
-   Filament 4.x

## Installation

Install the package via composer:

```bash
composer require joaoolival/laravel-blog-engine
```

Run the install command:

```bash
php artisan blog-engine:install
```

The installer will guide you through publishing migrations, running them, setting up Filament, and optionally creating an admin user.

### Storage Link

To display uploaded images correctly, create the storage symbolic link:

```bash
php artisan storage:link
```

## Models

The package provides three Eloquent models that you can use directly in your application.

### BlogPost

```php
use Joaoolival\LaravelBlogEngine\Models\BlogPost;
```

| Attribute          | Type           | Description                   |
| ------------------ | -------------- | ----------------------------- |
| `id`               | `int`          | Primary key                   |
| `title`            | `string`       | Post title                    |
| `slug`             | `string`       | URL-friendly slug (unique)    |
| `excerpt`          | `string\|null` | Short summary                 |
| `content`          | `string\|null` | Full post content (rich text) |
| `tags`             | `array\|null`  | JSON array of tags            |
| `is_visible`       | `bool`         | Visibility status             |
| `published_at`     | `Carbon\|null` | Publish date/time             |
| `blog_author_id`   | `int\|null`    | Foreign key to author         |
| `blog_category_id` | `int\|null`    | Foreign key to category       |

**Relationships:**

-   `author()` → `BelongsTo` BlogAuthor
-   `category()` → `BelongsTo` BlogCategory

**Scopes:**

```php
// Get only published posts (visible + published_at <= now)
BlogPost::whereIsPublished()->get();

// Get draft posts (not published yet)
BlogPost::whereIsDraft()->get();

// Get visible posts
BlogPost::whereIsVisible()->get();
```

**Media Collections:**

-   `gallery` - Post images
-   `content-attachments` - Rich editor attachments

### BlogAuthor

```php
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
```

| Attribute          | Type           | Description           |
| ------------------ | -------------- | --------------------- |
| `id`               | `int`          | Primary key           |
| `name`             | `string`       | Author name           |
| `slug`             | `string\|null` | URL-friendly slug     |
| `email`            | `string`       | Author email (unique) |
| `bio`              | `string\|null` | Author biography      |
| `is_visible`       | `bool`         | Visibility status     |
| `github_handle`    | `string\|null` | GitHub username       |
| `twitter_handle`   | `string\|null` | Twitter/X username    |
| `linkedin_handle`  | `string\|null` | LinkedIn username     |
| `instagram_handle` | `string\|null` | Instagram username    |
| `facebook_handle`  | `string\|null` | Facebook username     |

**Relationships:**

-   `posts()` → `HasMany` BlogPost

**Media Collections:**

-   `avatar` - Author profile picture (single file)

### BlogCategory

```php
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
```

| Attribute         | Type           | Description                |
| ----------------- | -------------- | -------------------------- |
| `id`              | `int`          | Primary key                |
| `name`            | `string`       | Category name              |
| `slug`            | `string`       | URL-friendly slug (unique) |
| `description`     | `string\|null` | Category description       |
| `is_visible`      | `bool`         | Visibility status          |
| `seo_title`       | `string\|null` | SEO meta title             |
| `seo_description` | `string\|null` | SEO meta description       |

**Relationships:**

-   `posts()` → `HasMany` BlogPost

**Media Collections:**

-   `banner_image` - Category banner (single file)

## Query Examples

```php
use Joaoolival\LaravelBlogEngine\Models\BlogPost;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;

// Get all published posts with author and category
$posts = BlogPost::whereIsPublished()
    ->with(['author', 'category'])
    ->latest('published_at')
    ->get();

// Get a post by slug
$post = BlogPost::where('slug', 'my-post-slug')
    ->whereIsPublished()
    ->firstOrFail();

// Get posts by category
$category = BlogCategory::where('slug', 'tutorials')->first();
$posts = $category->posts()->whereIsPublished()->get();

// Get posts by author
$author = BlogAuthor::where('slug', 'john-doe')->first();
$posts = $author->posts()->whereIsPublished()->get();

// Get all visible categories with post count
$categories = BlogCategory::whereIsVisible()
    ->withCount(['posts' => fn($q) => $q->whereIsPublished()])
    ->get();

// Get featured image from post gallery
$post = BlogPost::find(1);
$featuredImage = $post->getFirstMediaUrl('gallery', 'webp');

// Get author avatar
$author = BlogAuthor::find(1);
$avatarUrl = $author->getFirstMediaUrl('avatar', 'webp');
```

## Responsive Images & SEO

This package uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for media management. When you have [Laravel Horizon](https://laravel.com/docs/horizon) configured, the package will automatically generate responsive image variants in the background. This provides:

-   **Faster page loads** - Appropriately sized images for each device
-   **Better SEO scores** - Optimized images improve Core Web Vitals
-   **Reduced bandwidth** - Smaller images for mobile users

To enable background processing:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Admin Panel

After installation, access the admin at `/admin`:

| Resource   | URL                      |
| ---------- | ------------------------ |
| Dashboard  | `/admin`                 |
| Blog Posts | `/admin/blog-posts`      |
| Authors    | `/admin/blog-authors`    |
| Categories | `/admin/blog-categories` |

### Manual Plugin Registration

If needed, manually register the plugin in your panel provider:

```php
use Joaoolival\LaravelBlogEngine\BlogPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(BlogPlugin::make());
}
```

## Configuration

Publish the configuration file:

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
