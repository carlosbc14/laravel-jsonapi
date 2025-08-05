<?php

namespace App\Http\Requests\Article;

use App\Http\Requests\JsonApiRequest;

class IndexArticleRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'articles';
    }

    /**
     * Get the allowed sorts for the resource.
     */
    protected function getAllowedSorts(): array
    {
        return [
            'title',
            'slug',
            'content',
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
            'articles' => ['title', 'slug', 'content', 'created_at', 'updated_at'],
            'users' => ['name', 'email', 'created_at', 'updated_at'],
            'categories' => ['name', 'slug', 'created_at', 'updated_at'],
        ];
    }

    /**
     * Get the allowed includes for the resource.
     */
    protected function getAllowedIncludes(): array
    {
        return [
            'user',
            'category',
            'category.user',
        ];
    }

    /**
     * Get the allowed filters for the resource.
     */
    protected function getAllowedFilters(): array
    {
        return [
            'title' => 'string',
            'slug' => 'string',
            'content' => 'string',
            'user_id' => 'integer|exists:users,id',
            'category_id' => 'integer|exists:categories,id',
            'created_at' => 'date',
            'updated_at' => 'date',
        ];
    }
}
