<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\IndexArticleRequest;
use App\Http\Requests\Article\ShowArticleRequest;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Category;
use App\Scopes\Filter;
use App\Scopes\Paginate;
use App\Scopes\Sort;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexArticleRequest $request): AnonymousResourceCollection
    {
        $query = Article::query();

        $articles = $query->with($request->getIncludes())
            ->tap(new Sort($request->getSort()))
            ->tap(new Filter($request->getFilters()));

        $pagination = $request->getPagination();
        return ArticleResource::collection($articles->pipe(new Paginate($pagination['size'], $pagination['number'])));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request): ArticleResource
    {
        $attributes = $request->validated()['data']['attributes'];
        $attributes['user_id'] = $request->user()->id;

        $category = Category::where('slug', $request->input('data.relationships.category.data.id'))->first();
        $attributes['category_id'] = $category->id;

        $article = Article::create($attributes);

        $includes = $request->getIncludes();
        if (!empty($includes)) $article->load($includes);

        return ArticleResource::make($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowArticleRequest $request, Article $article): ArticleResource
    {
        $includes = $request->getIncludes();
        if (!empty($includes)) $article->load($includes);

        return ArticleResource::make($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article): ArticleResource
    {
        if ($request->filled('data.attributes')) {
            $attributes = $request->validated()['data']['attributes'];
            $article->update($attributes);
        }

        if ($request->filled('data.relationships.category.data.id')) {
            $category = Category::where('slug', $request->input('data.relationships.category.data.id'))->first();
            $attributes['category_id'] = $category->id;
        }

        $includes = $request->getIncludes();
        if (!empty($includes)) $article->load($includes);

        return ArticleResource::make($article);
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
