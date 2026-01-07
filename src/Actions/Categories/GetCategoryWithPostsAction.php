<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Categories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetCategoryWithPostsAction
{
    /**
     * Fetch a category by slug along with its published posts.
     *
     * @return array{category: BlogCategory, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>}
     */
    public function handle(string $slug, ?int $perPage = null): array
    {
        $category = BlogCategory::query()
            ->with('media')
            ->where('slug', $slug)
            ->whereIsVisible()
            ->firstOrFail();

        $query = BlogPost::query()
            ->with(['media', 'author', 'category'])
            ->where('blog_category_id', $category->id)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at');

        $posts = $perPage === null ? $query->get() : $query->paginate($perPage);

        return [
            'category' => $category,
            'posts' => $posts,
        ];
    }
}
