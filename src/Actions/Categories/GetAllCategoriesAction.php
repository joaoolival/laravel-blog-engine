<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Categories;

use Illuminate\Database\Eloquent\Collection;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;

final readonly class GetAllCategoriesAction
{
    /**
     * Fetch all visible categories ordered by name.
     *
     * @return Collection<int, BlogCategory>
     */
    public function handle(): Collection
    {
        return BlogCategory::query()
            ->with('media')
            ->whereIsVisible()
            ->orderBy('name')
            ->get();
    }
}
