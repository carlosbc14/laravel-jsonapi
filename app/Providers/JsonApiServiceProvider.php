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
        Builder::macro('jsonApiSort', function (array $allowedSorts = []): Builder {
            /** @var Builder $this */
            if (!request()->filled('sort')) return $this;

            $sortFields = explode(',', request()->input('sort'));

            foreach ($sortFields as $sortField) {
                $sortDirection = str_starts_with($sortField, '-') ? 'desc' : 'asc';
                $sortField = ltrim($sortField, '-');

                abort_unless(empty($allowedSorts) || in_array($sortField, $allowedSorts), 400, 'Invalid sort field.');

                $this->orderBy($sortField, $sortDirection);
            }

            return $this;
        });
    }
}
