<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class SearchPostsAction
{
    /**
     * Search published posts by title, excerpt, or content.
     *
     * @return LengthAwarePaginator<int, BlogPost>|Collection<int, BlogPost>
     */
    public function handle(string $query, ?int $perPage = null): LengthAwarePaginator|Collection
    {
        $searchTerm = '%'.strtolower($query).'%';

        $builder = BlogPost::query()
            ->whereIsPublished()
            ->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(content) LIKE ?', [$searchTerm]);
            })
            ->with(['author', 'category'])
            ->orderByDesc('published_at');

        if ($perPage !== null) {
            return $builder->paginate($perPage);
        }

        return $builder->get();
    }
}
