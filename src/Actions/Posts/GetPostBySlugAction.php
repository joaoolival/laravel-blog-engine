<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetPostBySlugAction
{
    /**
     * Fetch a single published blog post by slug.
     */
    public function handle(string $slug): BlogPost
    {
        return BlogPost::query()
            ->with(['media', 'author.media', 'category'])
            ->whereIsPublished()
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
