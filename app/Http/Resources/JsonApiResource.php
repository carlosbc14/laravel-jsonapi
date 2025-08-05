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
        $data = [
            'type' => $this->getResourceType(),
            'id' => (string) $this->getRouteKey(),
            'attributes' => $this->getFilteredAttributes($request),
            'links' => [
                'self' => route($this->getResourceType() . '.show', $this->getRouteKey()),
            ],
        ];

        $relationships = $this->getResourceRelationships();

        if (!empty($relationships)) {
            $data['relationships'] = $relationships;
        }

        if ($request->filled('include')) {
            $this->with['included'] = $this->getIncludedResources($request);
        }

        return $data;
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

    protected function getFilteredAttributes(Request $request): array
    {
        $type = $this->getResourceType();

        $attributes = $this->resource->attributesToArray();
        unset($attributes['id']);

        if (!$request->filled("fields.$type")) return $attributes;

        $fields = explode(',', $request->input("fields.$type"));

        return Arr::only($attributes, $fields);
    }

    protected function getResourceRelationships(): array
    {
        $relationships = [];

        foreach ($this->resource->getRelations() as $relation => $model) {
            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                $relationships[$relation] = [
                    'data' => [
                        'type' => $model->getTable(),
                        'id' => (string) $model->getRouteKey(),
                    ],
                ];
            } elseif ($model instanceof \Illuminate\Support\Collection) {
                $relationships[$relation] = [
                    'data' => $model->map(function ($item) {
                        return [
                            'type' => $item->getTable(),
                            'id' => (string) $item->getRouteKey(),
                        ];
                    })->all(),
                ];
            }
        }

        return $relationships;
    }

    protected function getIncludedResources(Request $request): array
    {
        if (!$request->filled('include')) return [];

        $includes = explode(',', $request->input('include'));
        $includedMap = [];

        foreach ($includes as $include) {
            $parts = explode('.', $include);
            $this->extractIncluded($this->resource, $parts, $includedMap);
        }

        return array_values($includedMap);
    }

    protected function extractIncluded($model, array $parts, array &$includedMap): void
    {
        $relationName = array_shift($parts);
        if (!$model->relationLoaded($relationName)) return;

        $related = $model->$relationName;

        $addIncluded = function ($item) use (&$includedMap, $parts) {
            $resource = static::make($item);
            $key = $item->getTable() . ':' . $item->getRouteKey();

            if (!isset($includedMap[$key])) {
                $includedMap[$key] = $resource;

                if (!empty($parts)) {
                    $this->extractIncluded($item, $parts, $includedMap);
                }
            }
        };

        if ($related instanceof \Illuminate\Database\Eloquent\Model) {
            $addIncluded($related);
        } elseif ($related instanceof \Illuminate\Support\Collection) {
            foreach ($related as $item) {
                $addIncluded($item);
            }
        }
    }
}
