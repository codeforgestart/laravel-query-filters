<?php

namespace Vendor\LaravelQueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Vendor\LaravelQueryFilters\Contracts\FilterInterface;

abstract class Filter implements FilterInterface
{
    protected array $filters = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function apply(Builder $query): Builder
    {
        foreach ($this->filters() as $key => $method) {

            if (!$this->hasValue($key)) {
                continue;
            }

            $this->{$method}(
                $query,
                $this->filters[$key]
            );
        }

        return $query;
    }

    /**
     * Define available filters.
     *
     * Example:
     *
     * return [
     *     'search' => 'search',
     *     'status' => 'status',
     * ];
     */
    abstract protected function filters(): array;

    protected function hasValue(string $key): bool
    {
        return array_key_exists($key, $this->filters)
            && $this->filters[$key] !== null
            && $this->filters[$key] !== '';
    }

    protected function value(string $key, mixed $default = null): mixed
    {
        return $this->filters[$key] ?? $default;
    }
}