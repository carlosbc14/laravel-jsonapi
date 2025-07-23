<?php

namespace App\Http\Controllers;

use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonApiCollection
    {
        /** @var \Illuminate\Database\Eloquent\Builder $categories */
        $users = User::with(['articles', 'categories'])
            ->withAllowedSorts(['name', 'email', 'created_at', 'updated_at'])
            ->withAllowedFilters(['name', 'email', 'created_at', 'updated_at']);

        return JsonApiCollection::make($users->paginateAsJsonApi());
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonApiResource
    {
        return JsonApiResource::make($user);
    }
}
