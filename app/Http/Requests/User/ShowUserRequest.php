<?php

namespace App\Http\Requests\User;

use App\Http\Requests\JsonApiRequest;

class ShowUserRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'users';
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
}
