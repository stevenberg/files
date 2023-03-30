<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ThumbnailController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [FolderController::class, 'index'])
    ->name('home')
;

Route::post('/folders/{folder}/restore', [FolderController::class, 'restore'])
    ->withTrashed()
    ->name('folders.restore')
;

Route::resource('folders', FolderController::class)
    ->only([
        'show',
        'create',
        'store',
        'update',
        'destroy',
    ])
    ->withTrashed([
        'destroy',
    ])
;

Route::post('/folders/{folder}/entries/{entry}/restore', [EntryController::class, 'restore'])
    ->withTrashed()
    ->name('folders.entries.restore')
;

Route::resource('folders.entries', EntryController::class)
    ->only([
        'create',
        'store',
        'update',
        'destroy',
    ])
    ->withTrashed([
        'destroy',
    ])
    ->scoped()
;

Route::resource('folders.files', FileController::class)
    ->only([
        'show',
    ])
    ->parameters([
        'files' => 'entry',
    ])
    ->scoped()
;

Route::get(
    '/thumbnails/{thumbnail}/shape/{shape}/size/{size}',
    [ThumbnailController::class, 'show'],
)
    ->name('thumbnails.show')
;

Route::singleton('trash', TrashController::class)
    ->only([
        'show',
        'update',
    ])
;

Route::singleton('account', AccountController::class)
    ->only([
        'show',
    ])
;
