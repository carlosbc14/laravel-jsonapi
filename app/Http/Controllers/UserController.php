<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\ShowUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Scopes\Filter;
use App\Scopes\Paginate;
use App\Scopes\Sort;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexUserRequest $request): AnonymousResourceCollection
    {
        $query = User::query();

        $users = $query->with($request->getIncludes())
            ->tap(new Sort($request->getSort()))
            ->tap(new Filter($request->getFilters()));

        $pagination = $request->getPagination();
        return UserResource::collection($users->pipe(new Paginate($pagination['size'], $pagination['number'])));
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowUserRequest $request, User $user): UserResource
    {
        $includes = $request->getIncludes();
        if (!empty($includes)) $user->load($includes);

        return UserResource::make($user);
    }
}
