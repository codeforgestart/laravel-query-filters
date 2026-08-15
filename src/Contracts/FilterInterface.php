<?php

namespace CodeForgeStart\LaravelQueryFilters\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    /**
     * Apply the filter to the query.
     */
    public function apply(Builder $query): Builder;
}