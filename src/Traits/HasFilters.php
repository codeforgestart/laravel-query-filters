<?php

namespace Vendor\LaravelQueryFilters\Traits;

use Illuminate\Database\Eloquent\Builder;
use Vendor\LaravelQueryFilters\Contracts\FilterInterface;

trait HasFilters
{
    public function scopeFilter(
        Builder $query,
        FilterInterface $filter
    ): Builder {
        return $filter->apply($query);
    }
}