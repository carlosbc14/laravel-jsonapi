<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\ShowCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Article;
use App\Models\Category;
use App\Scopes\Filter;
use App\Scopes\Paginate;
use App\Scopes\Sort;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCategoryRequest $request): AnonymousResourceCollection
    {
        $query = Category::query();

        $categories = $query->with($request->getIncludes())
            ->tap(new Sort($request->getSort()))
            ->tap(new Filter($request->getFilters()));

        $pagination = $request->getPagination();
        return CategoryResource::collection($categories->pipe(new Paginate($pagination['size'], $pagination['number'])));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $attributes = $request->validated()['data']['attributes'];
        $attributes['user_id'] = $request->user()->id;

        $category = Category::create($attributes);

        if ($request->filled('data.relationships.articles.data')) {
            $articles = $request->input('data.relationships.articles.data');
            Article::where('category_id', $category->id)->update(['category_id' => null]);
            Article::whereIn('slug', array_column($articles, 'id'))->update(['category_id' => $category->id]);
        }

        $includes = $request->getIncludes();
        if (!empty($includes)) $category->load($includes);

        return CategoryResource::make($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowCategoryRequest $request, Category $category): CategoryResource
    {
        $includes = $request->getIncludes();
        if (!empty($includes)) $category->load($includes);

        return CategoryResource::make($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        if ($request->filled('data.attributes')) {
            $attributes = $request->validated()['data']['attributes'];
            $category->update($attributes);
        }

        if ($request->filled('data.relationships.articles.data')) {
            $articles = $request->input('data.relationships.articles.data');
            Article::where('category_id', $category->id)->update(['category_id' => null]);
            Article::whereIn('slug', array_column($articles, 'id'))->update(['category_id' => $category->id]);
        }

        $includes = $request->getIncludes();
        if (!empty($includes)) $category->load($includes);

        return CategoryResource::make($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): Response
    {
        $category->delete();

        return response()->noContent();
    }
}
