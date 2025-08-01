<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class JsonApiCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->filled('include')) {
            $this->with['included'] = $this->getIncludedCollection($request);
        }

        return [
            'data' => $this->collection,
            'links' => [
                'self' => $request->fullUrl(),
            ],
        ];
    }

    protected function getIncludedCollection(Request $request): array
    {
        $includedMap = [];

        foreach ($this->collection as $resource) {
            $includedItems = JsonApiResource::make($resource)->getIncludedResources($request);

            foreach ($includedItems as $item) {
                $key = $item->resource->getTable() . ':' . $item->resource->getRouteKey();

                if (!isset($includedMap[$key])) {
                    $includedMap[$key] = $item;
                }
            }
        }

        return array_values($includedMap);
    }

}
