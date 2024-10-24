<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowingTagController;
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
        Route::post('/articles/{article_slug}/bookmark', [BookmarkController::class, 'store']);
    });

    Route::group(['as' => 'tag'], function () {
        Route::get('/tags', [TagController::class, 'index']);
        Route::post('/tags', [TagController::class, 'store']);
        Route::get('/tags/{slug}', [TagController::class, 'show']);
        Route::delete('/tags/{slug}', [TagController::class, 'destroy']);
        Route::post('tags/{tag_slug}/follow', [FollowingTagController::class, 'follow']);
        Route::delete('tags/{tag_slug}/unfollow', [FollowingTagController::class, 'unfollow']);
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
        Route::get('users/{user_name}/followings', [FollowingUserController::class, 'index']);
        Route::post('users/{user_name}/follow', [FollowingUserController::class, 'follow']);
        Route::delete('users/{user_name}/unfollow', [FollowingUserController::class, 'unfollow']);
        Route::get('/users/{user_name}/following-tags', [FollowingTagController::class, 'index']);
        Route::get('/users/{user_name}/bookmarks', [BookmarkController::class, 'index']);
    });

    Route::group(['as' => 'comment'], function () {
        Route::get('/article/{slug}/comments', [CommentController::class, 'index']);
        Route::post('/article/{slug}/comments', [CommentController::class, 'store']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
        Route::get('/comments/{comment_id}/replies', [CommentController::class, 'getSubComments']);
        Route::post('/comments/{comment_id}/replies', [CommentController::class, 'reply']);
    });
});
