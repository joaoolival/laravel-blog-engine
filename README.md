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

You can install the package via composer:

```bash
composer require joaoolival/laravel-blog-engine
```

Run the install command to set up everything automatically:

```bash
php artisan blog-engine:install
```

The installer will guide you through:

1. Publishing migrations
2. Running database migrations
3. Setting up the Filament panel
4. Publishing assets
5. Creating an admin user (optional)

### Storage Link

To display uploaded images correctly, you need to create a symbolic link from `public/storage` to `storage/app/public`:

```bash
php artisan storage:link
```

## Responsive Images & SEO

This package uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for media management. When you have [Laravel Horizon](https://laravel.com/docs/horizon) configured, the package will automatically generate responsive image variants in the background. This provides:

-   **Faster page loads** - Appropriately sized images for each device
-   **Better SEO scores** - Optimized images improve Core Web Vitals
-   **Reduced bandwidth** - Smaller images for mobile users

To enable background processing, simply install and configure Horizon:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

## Usage

After installation, access the admin panel at `/admin`. The blog engine adds these resources:

-   **Blog Posts** - `/admin/blog-posts`
-   **Authors** - `/admin/blog-authors`
-   **Categories** - `/admin/blog-categories`

### Manual Plugin Registration

If the installer couldn't automatically register the plugin, add it to your panel provider:

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
