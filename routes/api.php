<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User API routes for testing
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/paginated', [UserController::class, 'indexPaginated']);
