<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DatasetAnalysisController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User Operations
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::put('/user/name', [UserController::class, 'updateName']);
    Route::put('/user', fn(Request $request) => new UserResource($request->user()));
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dataset Operations
    Route::get('/datasets', [DatasetController::class, 'index']);
    Route::get('/datasets/{dataset}/overview', [DatasetAnalysisController::class, 'overview']);
    Route::get('/datasets/{dataset}/column-types', [DatasetAnalysisController::class, 'columnTypes']);
    Route::get('/datasets/{dataset}/distributions', [DatasetAnalysisController::class, 'distributions']);
    Route::get('/datasets/{dataset}/columns', [DatasetAnalysisController::class, 'columns']);
    Route::get('/datasets/{dataset}/correlation', [DatasetAnalysisController::class, 'correlation']);
    Route::get('/datasets/{dataset}/health', [DatasetAnalysisController::class, 'health']);
    Route::get('/datasets/{dataset}/missing', [DatasetAnalysisController::class, 'missing']);

    // Add new dataset
    Route::post('/datasets', [DatasetController::class, 'store']);
});
