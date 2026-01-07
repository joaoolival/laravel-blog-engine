# Changelog

All notable changes to `laravel-blog-engine` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-01-07

### Added

-   **Blog Facade** - New `Blog` facade for querying content with a clean API
-   **Actions** - Single-responsibility action classes for all queries:
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
-   Optional pagination - Pass `perPage` to paginate, or `null` to get all results
-   Model factories for `BlogPost`, `BlogAuthor`, and `BlogCategory`
-   Unit tests for all models (22 tests, 45 assertions)

### Changed

-   Updated README with comprehensive API documentation

## [1.0.0] - 2026-01-07

### Added

-   Initial release of Laravel Blog Engine
-   `BlogPost` model with rich text content, tags, and publishing workflow
-   `BlogAuthor` model with social media handles and bio
-   `BlogCategory` model with SEO fields
-   Filament admin panel integration via `BlogPlugin`
-   Automatic responsive image generation using Spatie Media Library
-   `blog-engine:install` command for guided setup
-   Scopes: `whereIsPublished()`, `whereIsDraft()`, `whereIsVisible()`
-   Media collections for posts (gallery), authors (avatar), and categories (banner)
-   Soft deletes support for all models
