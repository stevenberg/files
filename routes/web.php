<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ThumbnailController;
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

Route::resource('folders', FolderController::class)
    ->only([
        'show',
        'create',
        'store',
        'update',
        'delete',
    ])
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

Route::singleton('account', AccountController::class)
    ->only([
        'show',
    ])
;
