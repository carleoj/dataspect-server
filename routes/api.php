<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::get('/user', fn (Request $request) => new UserResource($request->user()));
    Route::post('/logout', [AuthController::class, 'logout']);
});
