<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('tasks/sort', [TaskController::class, 'sort'])
    ->name('tasks.sort')
    ->middleware('auth:sanctum');

Route::post('tasks/{task}/restore', [TaskController::class, 'restore'])
    ->name('tasks.restore')
    ->middleware('auth:sanctum');

Route::put('tasks/{task}/status', [TaskController::class, 'status'])
    ->name('tasks.status')
    ->middleware('auth:sanctum');
