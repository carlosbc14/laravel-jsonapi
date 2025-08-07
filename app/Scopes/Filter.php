<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;

class Filter
{
    protected array $operators = ['eq' => '=', 'gt' => '>', 'lt' => '<', 'gte' => '>=', 'lte' => '<='];

    public function __construct(
        protected array $filters = [],
    ) {}

    public function __invoke(Builder $query): void
    {
        foreach ($this->filters as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $op => $v) {
                    if (isset($this->operators[$op])) {
                        strtotime($v) !== false
                            ? $query->whereDate($field, $this->operators[$op], $v)
                            : $query->where($field, $this->operators[$op], $v);
                    }
                }
                continue;
            }

            $query->where($field, 'LIKE', "%$value%");
        }
    }
}
