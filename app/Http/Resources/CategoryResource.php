<?php

namespace App\Http\Resources;

class CategoryResource extends JsonApiResource
{
    protected function getResourceType(): string
    {
        return 'categories';
    }

    protected function getAvailableRelationships(): array
    {
        return ['articles', 'user'];
    }
}
