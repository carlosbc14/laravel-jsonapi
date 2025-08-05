<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\JsonApiRequest;

class IndexCategoryRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'categories';
    }

    /**
     * Get the allowed sorts for the resource.
     */
    protected function getAllowedSorts(): array
    {
        return [
            'name',
            'slug',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Get the allowed fields for sparse fieldsets.
     */
    protected function getAllowedFields(): array
    {
        return [
            'categories' => ['name', 'slug', 'created_at', 'updated_at'],
            'users' => ['name', 'email', 'created_at', 'updated_at'],
            'articles' => ['title', 'slug', 'content', 'created_at', 'updated_at'],
        ];
    }

    /**
     * Get the allowed includes for the resource.
     */
    protected function getAllowedIncludes(): array
    {
        return [
            'user',
            'articles',
            'articles.user',
        ];
    }

    /**
     * Get the allowed filters for the resource.
     */
    protected function getAllowedFilters(): array
    {
        return [
            'name' => 'string',
            'slug' => 'string',
            'user_id' => 'integer|exists:users,id',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }
}
