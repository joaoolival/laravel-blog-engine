<?php

namespace Joaoolival\LaravelBlogEngine\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder<static> whereIsVisible()
 */
trait HasVisibility
{
    /**
     * @param  Builder<static>  $query
     */
    public function scopeWhereIsVisible(Builder $query): void
    {
        $query->where('is_visible', true);
    }
}
