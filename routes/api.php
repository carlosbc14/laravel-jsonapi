<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\AuthenticatedUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->withoutMiddleware(\App\Http\Middleware\EnsureJsonApiDocument::class)->middleware('auth:sanctum')->name('logout');

Route::get('/user', [AuthenticatedUserController::class, 'show'])->middleware('auth:sanctum')->name('user');

Route::apiResource('/users', UserController::class)->only(['index', 'show']);

Route::apiResource('/articles', ArticleController::class)->only(['index', 'show']);
Route::apiResource('/articles', ArticleController::class)->only(['store', 'update', 'destroy'])->middleware('auth:sanctum');

Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('/categories', CategoryController::class)->only(['store', 'update', 'destroy'])->middleware('auth:sanctum');
