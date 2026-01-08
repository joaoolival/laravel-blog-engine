<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetRelatedPostsAction
{
    /**
     * Get posts related to the given post (same category).
     *
     * @return Collection<int, BlogPost>
     */
    public function handle(BlogPost $post, int $limit = 4): Collection
    {
        $query = BlogPost::query()
            ->whereIsPublished()
            ->where('id', '!=', $post->id);

        if ($post->blog_category_id) {
            $query->where('blog_category_id', $post->blog_category_id);
        } else {
            return new Collection;
        }

        return $query
            ->with(['author', 'category'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }
}
