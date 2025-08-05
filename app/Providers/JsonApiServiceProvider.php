<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Builder::macro('withAllowedSorts', function (): Builder {
            /** @var Builder $this */
            if (!request()->filled('sort')) return $this;

            $sortFields = explode(',', request()->input('sort'));

            foreach ($sortFields as $sortField) {
                $sortDirection = str_starts_with($sortField, '-') ? 'desc' : 'asc';
                $sortField = ltrim($sortField, '-');

                $this->orderBy($sortField, $sortDirection);
            }

            return $this;
        });

        Builder::macro('withAllowedFilters', function (): Builder {
            /** @var Builder $this */
            $filters = request()->input('filter', []);
            $operators = ['eq' => '=', 'gt' => '>', 'lt' => '<', 'gte' => '>=', 'lte' => '<='];

            foreach ($filters as $filterField => $filterValue) {
                if (is_array($filterValue)) {
                    foreach ($filterValue as $op => $value) {
                        if (array_key_exists($op, $operators)) {
                            strtotime($value) !== false
                                ? $this->whereDate($filterField, $operators[$op], $value)
                                : $this->where($filterField, $operators[$op], $value);
                        }
                    }
                    continue;
                }

                $this->where($filterField, 'LIKE', "%$filterValue%");
            }

            return $this;
        });

        Builder::macro('paginateAsJsonApi', function (): LengthAwarePaginator {
            /** @var Builder $this */
            return $this->paginate(
                request()->input('page.size', 15),
                ['*'],
                'page[number]',
                request()->input('page.number', 1)
            )->appends(request()->except('page.number'));
        });
    }
}
