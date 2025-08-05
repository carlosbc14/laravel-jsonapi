<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): JsonApiCollection
    {
        $query = User::query();

        $users = $query->with($request->getIncludes())->withAllowedSorts()->withAllowedFilters();

        return JsonApiCollection::make($users->paginateAsJsonApi());
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowUserRequest $request, User $user): JsonApiResource
    {
        $includes = $request->getIncludes();
        if (!empty($includes)) $user->load($includes);

        return JsonApiResource::make($user);
    }
}
