<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\JsonApiRequest;

class StoreCategoryRequest extends JsonApiRequest
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

    /**
     * Get the allowed attributes for the resource.
     */
    protected function getAllowedAttributes(): array
    {
        return [
            'name' => ['required', 'string', 'min:4', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:categories,slug',
            ],
        ];
    }

    /**
     * Get the allowed relationships for the resource.
     */
    protected function getAllowedRelationships(): array
    {
        return [
            'articles' => [
                'type' => 'articles',
                'required' => false,
                'to_many' => true,
                'exists_rule' => 'exists:articles,' . (new \App\Models\Article)->getRouteKeyName(),
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
