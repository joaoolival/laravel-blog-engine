# Changelog

All notable changes to `laravel-blog-engine` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-01-07

### Added

-   Initial release of Laravel Blog Engine
-   **Requirements:** PHP 8.3+ and Laravel 12.0+
-   **Blog Facade** - Clean API for querying content
-   **Actions** - Single-responsibility action classes:
    -   `GetPublishedPostsAction` - Fetch published posts (paginated or all)
    -   `GetPostBySlugAction` - Fetch single post by slug
    -   `GetAllAuthorsAction` - Fetch all visible authors
    -   `GetAuthorWithPostsAction` - Fetch author with their posts
    -   `GetAllCategoriesAction` - Fetch all visible categories
    -   `GetCategoryWithPostsAction` - Fetch category with its posts
-   **HTTP Resources** - API Resources with responsive image handling:
    -   `BlogPostResource` / `BlogPostCollection`
    -   `BlogAuthorResource` / `BlogAuthorCollection`
    -   `BlogCategoryResource` / `BlogCategoryCollection`
-   **Content Caching** - `rendered_content` column caches processed HTML with responsive images to eliminate per-request processing overhead
-   `RegenerateRenderedContentAction` for manual cache regeneration
-   Filament hooks (`afterCreate`, `afterSave`) to automatically regenerate cached content when editing in admin panel
-   **Models:**
    -   `BlogPost` - Rich text content, tags, and publishing workflow
    -   `BlogAuthor` - Social media handles and bio
    -   `BlogCategory` - SEO fields
-   Filament admin panel integration via `BlogPlugin`
-   Automatic responsive image generation using Spatie Media Library
-   `blog-engine:install` command for guided setup
-   Scopes: `whereIsPublished()`, `whereIsDraft()`, `whereIsVisible()`
-   Media collections for posts (gallery), authors (avatar), and categories (banner)
-   Soft deletes support for all models
-   Optional pagination - Pass `perPage` to paginate, or `null` to get all results
-   Model factories for `BlogPost`, `BlogAuthor`, and `BlogCategory`
-   Comprehensive test suite (57 tests, 138 assertions) organized by domain
