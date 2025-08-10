<?php

namespace App\Http\Resources;

class UserResource extends JsonApiResource
{
    protected function getResourceType(): string
    {
        return 'users';
    }

    protected function getAvailableRelationships(): array
    {
        return ['articles', 'categories'];
    }
}
