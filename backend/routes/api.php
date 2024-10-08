<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/articles', [ArticleController::class, 'index']);
Route::post('/articles', [ArticleController::class, 'store']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::put('/articles/{slug}', [ArticleController::class, 'update']);
Route::delete('/articles/{slug}', [ArticleController::class, 'destroy']);
