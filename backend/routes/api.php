<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::group(['as' => 'article'], function () {
        Route::get('/articles', [ArticleController::class, 'index']);
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::get('/articles/{slug}', [ArticleController::class, 'show']);
        Route::put('/articles/{slug}', [ArticleController::class, 'update']);
        Route::delete('/articles/{slug}', [ArticleController::class, 'destroy']);
    });

    Route::group(['as' => 'tag'], function () {
        Route::get('/tags', [TagController::class, 'index']);
        Route::post('/tags', [TagController::class, 'store']);
        Route::get('/tags/{slug}', [TagController::class, 'show']);
        Route::delete('/tags/{slug}', [TagController::class, 'destroy']);
    });

    Route::group(['as' => 'image'], function () {
        Route::post('/images', [ImageController::class, 'store']);
        Route::get('/images', [ImageController::class, 'index']);
        Route::get('/images/{name}', [ImageController::class, 'show']);
        Route::delete('/images/{name}', [ImageController::class, 'destroy']);
    });

    Route::group(['as' => 'user'], function () {
        Route::get('users/me', [UserController::class, 'show']);
    });
});
