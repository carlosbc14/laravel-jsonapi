<?php

namespace App\Http\Requests\Article;

use App\Http\Requests\JsonApiRequest;

class UpdateArticleRequest extends JsonApiRequest
{
    /**
     * Get the resource type for the request.
     */
    protected function getResourceType(): string
    {
        return 'articles';
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
     * Get the allowed attributes for the resource.
     */
    protected function getAllowedAttributes(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:4', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:articles,slug,' . $this->route('article')->id,
            ],
            'content' => ['sometimes', 'string'],
        ];
    }

    /**
     * Get the allowed relationships for the resource.
     */
    protected function getAllowedRelationships(): array
    {
        return [
            'category' => [
                'type' => 'categories',
                'required' => false,
                'to_many' => false,
                'exists_rule' => 'exists:categories,' . (new \App\Models\Category)->getRouteKeyName(),
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
