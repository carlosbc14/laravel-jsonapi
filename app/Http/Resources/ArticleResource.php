<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'articles',
            'id' => (string) $this->getRouteKey(),
            'attributes' => array_filter([
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ], function ($value) use ($request) {
                if (!$request->filled('fields')) return true;

                $fields = explode(',', $request->input('fields.articles', ''));
                if ($value === $this->getRouteKey()) return in_array($this->getRouteKeyName(), $fields);

                return $value;
            }),
            'links' => [
                'self' => route('articles.show', $this->getRouteKey()),
            ],
        ];
    }

    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request)
    {
        return parent::toResponse($request)->withHeaders([
            'Location' => route('articles.show', $this->getRouteKey()),
        ]);
    }
}
