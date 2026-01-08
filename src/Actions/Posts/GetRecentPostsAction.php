<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetRecentPostsAction
{
    /**
     * Get the most recent published posts.
     *
     * @return Collection<int, BlogPost>
     */
    public function handle(int $limit = 5): Collection
    {
        return BlogPost::query()
            ->whereIsPublished()
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
