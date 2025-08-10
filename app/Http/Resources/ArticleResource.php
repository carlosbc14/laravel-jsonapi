<?php

namespace App\Http\Resources;

class ArticleResource extends JsonApiResource
{
    protected function getResourceType(): string
    {
        return 'articles';
    }

    protected function getAvailableRelationships(): array
    {
        return ['category', 'user'];
    }
}
