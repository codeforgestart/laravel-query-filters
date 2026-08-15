<?php

namespace Vendor\LaravelQueryFilters\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public function apply(Builder $query): Builder;
}