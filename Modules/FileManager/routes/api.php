<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\FileManager\app\Http\Controllers\Api\FileController;

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

Route::prefix('files')->group(function () {
    Route::post('/upload', [FileController::class, 'frontUpload']);
    Route::get('/{file}', [FileController::class, 'show'])->whereNumber('file');
});
