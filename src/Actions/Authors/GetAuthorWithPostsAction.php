<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Authors;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class GetAuthorWithPostsAction
{
    /**
     * Fetch an author by slug along with their published posts.
     *
     * @return array{author: BlogAuthor, posts: LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>}
     */
    public function handle(string $slug, ?int $perPage = null): array
    {
        $author = BlogAuthor::query()
            ->with('media')
            ->withCount(['posts' => fn ($query) => $query->whereNotNull('published_at')])
            ->where('slug', $slug)
            ->firstOrFail();

        $query = BlogPost::query()
            ->with(['media', 'author', 'category'])
            ->where('blog_author_id', $author->id)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at');

        $posts = $perPage === null ? $query->get() : $query->paginate($perPage);

        return [
            'author' => $author,
            'posts' => $posts,
        ];
    }
}
