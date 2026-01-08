<?php

namespace Joaoolival\LaravelBlogEngine\Facades;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;
use Joaoolival\LaravelBlogEngine\BlogService;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

/**
 * @method static LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost> getPublishedPosts(?int $perPage = null)
 * @method static BlogPost getPostBySlug(string $slug)
 * @method static Collection<int, BlogPost> getRecentPosts(int $limit = 5)
 * @method static Collection<int, BlogPost> getRelatedPosts(BlogPost $post, int $limit = 4)
 * @method static LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost> searchPosts(string $query, ?int $perPage = null)
 * @method static Collection<int, BlogAuthor> getAllAuthors()
 * @method static array{author: BlogAuthor, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>} getAuthorWithPosts(string $slug, ?int $perPage = null)
 * @method static Collection<int, BlogCategory> getAllCategories()
 * @method static array{category: BlogCategory, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>} getCategoryWithPosts(string $slug, ?int $perPage = null)
 *
 * @see \Joaoolival\LaravelBlogEngine\BlogService
 */
class Blog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BlogService::class;
    }
}
