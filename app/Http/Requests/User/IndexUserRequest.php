<?php

namespace App\Http\Requests\User;

use App\Http\Requests\JsonApiRequest;

class IndexUserRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'users';
    }

    /**
     * Get the allowed sorts for the resource.
     */
    protected function getAllowedSorts(): array
    {
        return [
            'name',
            'email',
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
            'users' => ['name', 'email', 'created_at', 'updated_at'],
            'articles' => ['title', 'slug', 'content', 'created_at', 'updated_at'],
            'categories' => ['name', 'slug', 'created_at', 'updated_at'],
        ];
    }

    /**
     * Get the allowed includes for the resource.
     */
    protected function getAllowedIncludes(): array
    {
        return [
            'articles',
            'categories',
        ];
    }

    /**
     * Get the allowed filters for the resource.
     */
    protected function getAllowedFilters(): array
    {
        return [
            'name' => 'string',
            'email' => 'string',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }
}
