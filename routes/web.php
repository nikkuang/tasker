<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('tasks', [TaskController::class, 'index'])
    ->name('tasks.index')
    ->middleware(['auth', 'verified']);

Route::post('tasks', [TaskController::class, 'store'])
    ->name('tasks.store')
    ->middleware('auth');

Route::get('tasks/report', [TaskReportController::class, 'report'])
    ->name('tasks.report')
    ->middleware(['auth', 'verified']);

Route::get('tasks/report/download', [TaskReportController::class, 'reportDownload'])
    ->name('tasks.report.download')
    ->middleware(['auth', 'verified']);

Route::get('tasks/trashed', [TaskController::class, 'trashed'])
    ->name('tasks.trashed')
    ->middleware(['auth', 'verified']);

Route::get('tasks/{task}', [TaskController::class, 'show'])
    ->name('tasks.show')
    ->middleware('auth');

Route::put('tasks/{task}', [TaskController::class, 'update'])
    ->name('tasks.update')
    ->middleware('auth');

Route::post('tasks/{task}/subtask', [TaskController::class, 'subtask'])
    ->name('tasks.subtasks')
    ->middleware('auth');

Route::put('tasks/{task}', [TaskController::class, 'update'])
    ->name('tasks.update')
    ->middleware('auth');

Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
    ->name('tasks.destroy')
    ->middleware('auth');

Route::delete('tasks/{task}/trash', [TaskController::class, 'trash'])
    ->name('tasks.trash')
    ->middleware('auth');

require __DIR__.'/auth.php';
