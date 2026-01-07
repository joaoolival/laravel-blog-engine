# Changelog

All notable changes to `laravel-blog-engine` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
