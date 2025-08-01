<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonApiCollection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $articles */
        $articles = Article::with(['user', 'category'])
            ->withAllowedSorts(['title', 'slug', 'content', 'created_at', 'updated_at'])
            ->withAllowedFilters(['title', 'slug', 'content', 'created_at', 'updated_at']);

        return JsonApiCollection::make($articles->paginateAsJsonApi());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): JsonApiResource
    {
        $attributes = $request->validated()['data']['attributes'];
        $attributes['user_id'] = $request->user()->id;

        $category = Category::where('slug', $request->input('data.relationships.category.data.id'))->first();
        $attributes['category_id'] = $category->id;

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
    public function update(UpdateArticleRequest $request, Article $article): JsonApiResource
    {
        $attributes = $request->validated()['data']['attributes'];

        if ($request->filled('data.relationships.category.data.id')) {
            $category = Category::where('slug', $request->input('data.relationships.category.data.id'))->first();
            $attributes['category_id'] = $category->id;
        }

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
