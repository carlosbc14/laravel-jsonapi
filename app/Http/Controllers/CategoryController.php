<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonApiCollection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $categories */
        $categories = Category::with(['author', 'articles'])
            ->withAllowedSorts(['name', 'slug', 'created_at', 'updated_at'])
            ->withAllowedFilters(['name', 'slug', 'created_at', 'updated_at']);

        return JsonApiCollection::make($categories->paginateAsJsonApi());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonApiResource
    {
        $attributes = $request->validated()['data']['attributes'];
        $attributes['user_id'] = $request->user()->id;

        $article = Category::create($attributes);

        return JsonApiResource::make($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): JsonApiResource
    {
        return JsonApiResource::make($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonApiResource
    {
        $attributes = $request->validated()['data']['attributes'];

        $category->update($attributes);

        return JsonApiResource::make($category);
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
