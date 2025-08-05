<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\JsonApiRequest;

class ShowCategoryRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'categories';
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
}
