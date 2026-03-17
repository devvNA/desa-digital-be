<?php

use App\Http\Controllers\HeadofFamilyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('user', UserController::class);
Route::get('user/all/paginated', [UserController::class, 'getAllPaginated']);

Route::apiResource('head-of-family', HeadofFamilyController::class);
Route::get('head-of-family/all/paginated', [HeadofFamilyController::class, 'getAllPaginated']);
