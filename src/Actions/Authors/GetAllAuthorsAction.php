<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Authors;

use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;

final readonly class GetAllAuthorsAction
{
    /**
     * Fetch all visible authors ordered by name.
     *
     * @return Collection<int, BlogAuthor>
     */
    public function handle(): Collection
    {
        return BlogAuthor::query()
            ->with('media')
            ->orderBy('name')
            ->whereIsVisible()
            ->get();
    }
}
