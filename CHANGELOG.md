# Changelog

All notable changes to `laravel-blog-engine` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-08

### Added

-   Initial release of Laravel Blog Engine
-   **Requirements:** PHP 8.3+ and Laravel 12.0+

#### Blog Facade

Clean API for querying blog content:

-   `getPublishedPosts(?int $perPage)` - Published posts (paginated or all)
-   `getPostBySlug(string $slug)` - Single post by slug
-   `getRecentPosts(int $limit)` - Most recent posts (for sidebars)
-   `getRelatedPosts(BlogPost $post, int $limit)` - Related posts by category
-   `searchPosts(string $query, ?int $perPage)` - Search posts
-   `getAllAuthors()` - All visible authors
-   `getAuthorWithPosts(string $slug, ?int $perPage)` - Author with posts
-   `getAllCategories()` - All visible categories
-   `getCategoryWithPosts(string $slug, ?int $perPage)` - Category with posts

#### HTTP Resources

API Resources with responsive image handling:

-   `BlogPostResource` / `BlogPostCollection`
-   `BlogAuthorResource` / `BlogAuthorCollection`
-   `BlogCategoryResource` / `BlogCategoryCollection`

#### Content Rendering

-   `RenderContentAction` - Converts Tiptap JSON to HTML with responsive images
-   Automatic WebP conversion and `srcset` attributes
-   Lazy loading and async decoding

#### Models

-   `BlogPost` - Rich text content, tags, publishing workflow
-   `BlogAuthor` - Name, bio, social media handles
-   `BlogCategory` - SEO fields (title, description)

#### Query Scopes

-   `whereIsPublished()` - Published and visible posts
-   `whereIsDraft()` - Drafts and scheduled posts
-   `whereIsVisible()` - Visible content only

#### Features

-   Filament admin panel integration via `BlogPlugin`
-   Automatic responsive image generation (Spatie Media Library)
-   `blog-engine:install` command for guided setup
-   Media collections: posts (gallery), authors (avatar), categories (banner)
-   Soft deletes for all models
-   Optional pagination (pass `perPage` or `null` for all results)
-   Model factories for testing
-   Comprehensive test suite (62 tests, 147 assertions)
