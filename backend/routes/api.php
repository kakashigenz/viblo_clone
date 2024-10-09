<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'article'], function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::get('/articles/{slug}', [ArticleController::class, 'show']);
    Route::post('/articles/{slug}', [ArticleController::class, 'update']);
    Route::delete('/articles/{slug}', [ArticleController::class, 'destroy']);
});

Route::group(['as' => 'tag'], function () {
    Route::get('/tags', [TagController::class, 'index']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags/{slug}', [TagController::class, 'show']);
    Route::post('/tags/{slug}', [TagController::class, 'update']);
    Route::delete('/tags/{slug}', [TagController::class, 'destroy']);
});
