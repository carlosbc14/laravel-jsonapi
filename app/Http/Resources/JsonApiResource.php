<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class JsonApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->getResourceType(),
            'id' => (string) $this->getRouteKey(),
            'attributes' => $this->getFilteredAttributes($request),
            'links' => [
                'self' => route($this->getResourceType() . '.show', $this->getRouteKey()),
            ],
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, JsonResponse $response): void
    {
        if ($response->getStatusCode() === 201) {
            $response->headers->set('Location', route($this->getResourceType() . '.show', $this->getRouteKey()));
        }
    }

    protected function getResourceType(): string
    {
        return $this->resource->getTable();
    }

    protected function getResourceAttributes(): array
    {
        $attributes = $this->resource->getAttributes();

        unset($attributes['id']);

        return $attributes;
    }

    protected function getRequestedFields(Request $request): array|null
    {
        $type = $this->getResourceType();

        if (!$request->filled("fields.$type")) return null;

        return explode(',', $request->input("fields.$type"));
    }

    protected function getFilteredAttributes(Request $request): array
    {
        $attributes = $this->getResourceAttributes();
        $fields = $this->getRequestedFields($request);

        if (!$fields) return $attributes;

        return Arr::only($attributes, $fields); //TODO: Validate the fields
    }
}
