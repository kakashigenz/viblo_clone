<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowingTagController;
use App\Http\Controllers\Api\FollowingUserController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationEmailController;
use App\Http\Controllers\Api\VoteController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/spa-login', [AuthController::class, 'spaLogin']);
Route::post('/check-authorization', [AuthController::class, 'checkAuthorization']);

Route::get('/email/verify/{id}/{hash}', [VerificationEmailController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/email/verification-notification', [VerificationEmailController::class, 'resendEmail'])
        ->middleware(['throttle:6,1'])->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => 'verified'], function () {
        Route::group(['as' => 'article.', 'prefix' => 'articles'], function () {
            Route::post('/', [ArticleController::class, 'store']);
            Route::post('/{article_slug}/bookmark', [BookmarkController::class, 'store']);
            Route::delete('/{article_slug}/unbookmark', [BookmarkController::class, 'destroy']);
            Route::post('/{id}/upvote', [VoteController::class, 'upvote'])->name('upvote');
            Route::post('/{id}/downvote', [VoteController::class, 'downvote'])->name('downvote');
            Route::post('/{slug}/comments', [CommentController::class, 'store']);
            Route::put('/{slug}', [ArticleController::class, 'update']);
            Route::delete('/{slug}', [ArticleController::class, 'destroy']);
        });

        Route::group(['as.' => 'tag.', 'prefix' => 'tags'], function () {
            Route::get('/', [TagController::class, 'index']);
            Route::post('/', [TagController::class, 'store']);
            Route::get('/{slug}', [TagController::class, 'show']);
            Route::delete('/{slug}', [TagController::class, 'destroy']);
            Route::post('/{tag_slug}/follow', [FollowingTagController::class, 'follow']);
            Route::delete('/{tag_slug}/unfollow', [FollowingTagController::class, 'unfollow']);
        });

        Route::group(['as' => 'image.', 'prefix' => 'images'], function () {
            Route::post('/create-presigned-url', [ImageController::class, 'createPresignedURL']);
            Route::get('/', [ImageController::class, 'index']);
            Route::post('/', [ImageController::class, 'store']);
            Route::get('/{name}', [ImageController::class, 'show']);
            Route::delete('/{name}', [ImageController::class, 'destroy']);
        });

        Route::group(['as' => 'user.', 'prefix' => 'users'], function () {
            Route::get('/me', [UserController::class, 'getCurrentUser']);
            Route::get('/{user_name}', [UserController::class, 'show']);
            Route::put('/{user_name}', [UserController::class, 'update']);
            Route::get('/{user_name}/followings', [FollowingUserController::class, 'index']);
            Route::post('/{user_name}/follow', [FollowingUserController::class, 'follow']);
            Route::delete('/{user_name}/unfollow', [FollowingUserController::class, 'unfollow']);
            Route::get('/{user_name}/following-tags', [FollowingTagController::class, 'index']);
            Route::get('/{user_name}/bookmarks', [BookmarkController::class, 'index']);
        });

        Route::group(['as' => 'comment.', 'prefix' => 'comments'], function () {
            Route::put('/{id}', [CommentController::class, 'update']);
            Route::delete('/{id}', [CommentController::class, 'destroy']);
            Route::post('/{comment_id}/replies', [CommentController::class, 'reply']);
            Route::post('/{comment_id}/upvote', [VoteController::class, 'upvote'])->name('upvote');
            Route::post('/{comment_id}/downvote', [VoteController::class, 'downvote'])->name('downvote');
        });

        Route::group(['as' => 'notification.', 'prefix' => 'notifications'], function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::put('/mark-all-read', [NotificationController::class, 'markAllRead']);
            Route::put('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        });
    });
});

Route::group(['as' => 'article.', 'prefix' => 'articles'], function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('/{slug}', [ArticleController::class, 'show']);
    Route::get('/{slug}/comments', [CommentController::class, 'index']);
});
Route::group(['prefix' => 'comments'], function () {
    Route::get('/{comment_id}/replies', [CommentController::class, 'getSubComments']);
});


Route::group(['as' => 'search.', 'prefix' => 'search'], function () {
    Route::get('/articles', [SearchController::class, 'searchArticle']);
    Route::get('/users', [SearchController::class, 'searchUser']);
    Route::get('/multi', [SearchController::class, 'searchMulti']);
});
