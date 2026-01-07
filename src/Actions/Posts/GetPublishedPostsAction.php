<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetPublishedPostsAction
{
    /**
     * Fetch published blog posts.
     *
     * @return LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>
     */
    public function handle(?int $perPage = null): LengthAwarePaginator|Collection
    {
        $query = BlogPost::query()
            ->with(['media', 'author', 'category'])
            ->orderByDesc('published_at')
            ->whereIsPublished();

        if ($perPage === null) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }
}
