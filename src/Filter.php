<?php

namespace CodeForgeStart\LaravelQueryFilters;

use CodeForgeStart\LaravelQueryFilters\Contracts\FilterInterface;
use CodeForgeStart\LaravelQueryFilters\Exceptions\InvalidFilterException;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter implements FilterInterface
{
    /**
     * Request/filter values.
     */
    protected array $filters = [];

    /**
     * Current query builder.
     */
    protected Builder $query;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Apply configured filters to the query.
     */
    public function apply(Builder $query): Builder
    {
        $this->query = $query;

        foreach ($this->filters() as $key => $method) {
            if (!$this->hasValue($key)) {
                continue;
            }

            $this->applyFilter($key, $method);
        }

        return $this->query;
    }

    /**
     * Define allowed filters.
     *
     * Example:
     *
     * return [
     *     'search' => 'search',
     *     'status' => 'status',
     * ];
     */
    abstract protected function filters(): array;

    /**
     * Apply an individual filter.
     */
    protected function applyFilter(
        string $key,
        string $method
    ): void {
        if (!method_exists($this, $method)) {
            throw new InvalidFilterException(
                sprintf(
                    'Filter method [%s] configured for key [%s] does not exist on [%s].',
                    $method,
                    $key,
                    static::class
                )
            );
        }

        $this->{$method}(
            $this->query,
            $this->filters[$key]
        );
    }

    /**
     * Determine whether a filter has a usable value.
     */
    protected function hasValue(string $key): bool
    {
        if (!array_key_exists($key, $this->filters)) {
            return false;
        }

        $value = $this->filters[$key];

        if ($value === null) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_array($value) && $value === []) {
            return false;
        }

        return true;
    }

    /**
     * Get a filter value.
     */
    protected function value(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->filters[$key] ?? $default;
    }

    /**
     * Get all filter values.
     */
    protected function all(): array
    {
        return $this->filters;
    }

    /**
     * Get the current query builder.
     */
    protected function query(): Builder
    {
        return $this->query;
    }

    /*
    |--------------------------------------------------------------------------
    | Query Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Apply a where condition.
     */
    protected function where(
        string $column,
        mixed $operator,
        mixed $value = null
    ): static {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->query->where(
            $column,
            $operator,
            $value
        );

        return $this;
    }

    /**
     * Apply whereIn.
     */
    protected function whereIn(
        string $column,
        array $values
    ): static {
        $this->query->whereIn(
            $column,
            $values
        );

        return $this;
    }

    /**
     * Apply whereNotIn.
     */
    protected function whereNotIn(
        string $column,
        array $values
    ): static {
        $this->query->whereNotIn(
            $column,
            $values
        );

        return $this;
    }

    /**
     * Apply whereNull.
     */
    protected function whereNull(
        string $column
    ): static {
        $this->query->whereNull($column);

        return $this;
    }

    /**
     * Apply whereNotNull.
     */
    protected function whereNotNull(
        string $column
    ): static {
        $this->query->whereNotNull($column);

        return $this;
    }

    /**
     * Apply LIKE condition.
     */
    protected function whereLike(
        string $column,
        string $value
    ): static {
        $this->query->where(
            $column,
            'LIKE',
            '%' . $value . '%'
        );

        return $this;
    }

    /**
     * Apply starts-with condition.
     */
    protected function whereStartsWith(
        string $column,
        string $value
    ): static {
        $this->query->where(
            $column,
            'LIKE',
            $value . '%'
        );

        return $this;
    }

    /**
     * Apply ends-with condition.
     */
    protected function whereEndsWith(
        string $column,
        string $value
    ): static {
        $this->query->where(
            $column,
            'LIKE',
            '%' . $value
        );

        return $this;
    }

    /**
     * Apply a safe order by.
     *
     * Example:
     *
     * sort=name
     * sort=-name
     */
    protected function sortBy(
        string $value,
        array $allowed
    ): static {
        $direction = 'asc';

        if (str_starts_with($value, '-')) {
            $direction = 'desc';
            $value = substr($value, 1);
        }

        if (
            $value !== ''
            && in_array($value, $allowed, true)
        ) {
            $this->query->orderBy(
                $value,
                $direction
            );
        }

        return $this;
    }
}