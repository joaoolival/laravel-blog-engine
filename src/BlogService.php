<?php

namespace Joaoolival\LaravelBlogEngine;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Actions\Authors\GetAllAuthorsAction;
use Joaoolival\LaravelBlogEngine\Actions\Authors\GetAuthorWithPostsAction;
use Joaoolival\LaravelBlogEngine\Actions\Categories\GetAllCategoriesAction;
use Joaoolival\LaravelBlogEngine\Actions\Categories\GetCategoryWithPostsAction;
use Joaoolival\LaravelBlogEngine\Actions\Posts\GetPostBySlugAction;
use Joaoolival\LaravelBlogEngine\Actions\Posts\GetPublishedPostsAction;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

class BlogService
{
    /**
     * Get published blog posts.
     *
     * @param  int|null  $perPage  Pass null to get all posts without pagination
     * @return LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>
     */
    public function getPublishedPosts(?int $perPage = null): LengthAwarePaginator|Collection
    {
        return app(GetPublishedPostsAction::class)->handle($perPage);
    }

    /**
     * Get a single published blog post by slug.
     */
    public function getPostBySlug(string $slug): BlogPost
    {
        return app(GetPostBySlugAction::class)->handle($slug);
    }

    /**
     * Get all visible authors.
     *
     * @return Collection<int, BlogAuthor>
     */
    public function getAllAuthors(): Collection
    {
        return app(GetAllAuthorsAction::class)->handle();
    }

    /**
     * Get an author by slug with their published posts.
     *
     * @param  int|null  $perPage  Pass null to get all posts without pagination
     * @return array{author: BlogAuthor, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>}
     */
    public function getAuthorWithPosts(string $slug, ?int $perPage = null): array
    {
        return app(GetAuthorWithPostsAction::class)->handle($slug, $perPage);
    }

    /**
     * Get all visible categories.
     *
     * @return Collection<int, BlogCategory>
     */
    public function getAllCategories(): Collection
    {
        return app(GetAllCategoriesAction::class)->handle();
    }

    /**
     * Get a category by slug with its published posts.
     *
     * @param  int|null  $perPage  Pass null to get all posts without pagination
     * @return array{category: BlogCategory, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>}
     */
    public function getCategoryWithPosts(string $slug, ?int $perPage = null): array
    {
        return app(GetCategoryWithPostsAction::class)->handle($slug, $perPage);
    }
}
