<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowingUserController;
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
        Route::post('/images', [ImageController::class, 'createPresignedURL']);
        Route::get('/images', [ImageController::class, 'index']);
        Route::delete('/images/{name}', [ImageController::class, 'destroy']);
    });

    Route::group(['as' => 'user'], function () {
        Route::get('users/me', [UserController::class, 'getCurrentUser']);
        Route::get('users/{user_name}', [UserController::class, 'show']);
        Route::put('users/{user_name}', [UserController::class, 'update']);
    });

    Route::group(['as' => 'comment'], function () {
        Route::get('/article/{slug}/comments', [CommentController::class, 'index']);
        Route::post('/article/{slug}/comments', [CommentController::class, 'store']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
        Route::get('/comments/{comment_id}/replies', [CommentController::class, 'getSubComments']);
        Route::post('/comments/{comment_id}/replies', [CommentController::class, 'reply']);
    });


    Route::group(['as' => 'followingUser'], function () {
        Route::get('/followings/{user_id}', [FollowingUserController::class, 'index']);
        Route::post('/follow/{user_id}', [FollowingUserController::class, 'follow']);
        Route::post('/unfollow/{user_id}', [FollowingUserController::class, 'unfollow']);
    });
});
