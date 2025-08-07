<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

class Sort
{
    public function __construct(
        protected array $sortFields = [],
    ) {}

    public function __invoke(Builder $query): void
    {
        foreach ($this->sortFields as $field) {
            $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
            $field = ltrim($field, '-');

            $query->orderBy($field, $direction);
        }
    }
}
