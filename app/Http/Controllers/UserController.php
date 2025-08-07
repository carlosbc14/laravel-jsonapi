<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Resources\JsonApiCollection;
use App\Http\Resources\JsonApiResource;
use App\Models\User;
use App\Scopes\Filter;
use App\Scopes\Paginate;
use App\Scopes\Sort;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): JsonApiCollection
    {
        $query = User::query();

        $users = $query->with($request->getIncludes())
            ->tap(new Sort($request->getSort()))
            ->tap(new Filter($request->getFilters()));

        $pagination = $request->getPagination();
        return JsonApiCollection::make($users->pipe(new Paginate($pagination['size'], $pagination['number'])));
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
