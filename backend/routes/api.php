<?php

use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/article', [ArticleController::class, 'index']);
Route::post('/article', [ArticleController::class, 'store']);
Route::get('/article/{slug}', [ArticleController::class, 'show']);
Route::put('/article/{slug}', [ArticleController::class, 'update']);
Route::delete('/article/{slug}', [ArticleController::class, 'destroy']);
