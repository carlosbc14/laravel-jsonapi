<?php

namespace App\Scopes;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class Paginate
{
    public function __construct(
        protected int $pageSize = 15,
        protected int $pageNumber = 1,
    ) {}

    public function __invoke(Builder $query): LengthAwarePaginator
    {
        return $query->paginate(
            $this->pageSize,
            ['*'],
            'page[number]',
            $this->pageNumber,
        )->appends(request()->except('page.number'));
    }
}
