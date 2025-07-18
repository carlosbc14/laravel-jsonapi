<?php

namespace App\Http\Controllers;

use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonApiCollection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $articles */
        $articles = Article::query()
            ->jsonApiSort(['title', 'slug', 'content', 'created_at', 'updated_at'])
            ->jsonApiFilter(['title', 'slug', 'content', 'created_at', 'updated_at']);

        return JsonApiCollection::make($articles->jsonApiPaginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonApiResource
    {
        $request->validate([
            'data.type' => ['in:articles'],
        ]);

        $validated = $request->validate([
            'data.attributes.title' => ['required', 'string', 'min:4', 'max:255'],
            'data.attributes.slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:' . Article::class . ',slug',
            ],
            'data.attributes.content' => ['required', 'string'],
        ]);

        $attributes = $validated['data']['attributes'];
        $attributes['user_id'] = $request->user()->id;

        $article = Article::create($attributes);

        return JsonApiResource::make($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): JsonApiResource
    {
        return JsonApiResource::make($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article): JsonApiResource
    {
        $request->validate([
            'data.id' => ['in:' . $article->getRouteKey()],
            'data.type' => ['in:articles'],
        ]);

        $validated = $request->validate([
            'data.attributes.title' => ['sometimes', 'string', 'min:4', 'max:255'],
            'data.attributes.slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:' . Article::class . ',slug,' . $article->id,
            ],
            'data.attributes.content' => ['sometimes', 'string'],
        ]);

        $attributes = $validated['data']['attributes'];

        $article->update($attributes);

        return JsonApiResource::make($article);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
