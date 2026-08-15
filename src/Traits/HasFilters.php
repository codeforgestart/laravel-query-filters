<?php

namespace CodeForgeStart\LaravelQueryFilters\Traits;

use CodeForgeStart\LaravelQueryFilters\Contracts\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeFilter(
        Builder $query,
        FilterInterface|string $filter,
        ?array $filters = null
    ): Builder {
        if (is_string($filter)) {
            $filters ??= request()->query();

            $filter = app()->make(
                $filter,
                ['filters' => $filters]
            );
        }

        return $filter->apply($query);
    }
}