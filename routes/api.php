<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Models\SmsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return okResponse('Welcome to API');
});
Route::get('sms-log', function (Request $request) {
    SmsLog::create([
        'data' => json_encode($request->all()),
        'action' => SmsLog::ACTION_CHECK,
    ]);
});
Route::prefix('auth')->group(function () {
    Route::post('/sign-up', [AuthController::class, 'register']);
    Route::post('/confirm', [AuthController::class, 'confirm']);
    Route::post('/create-password', [AuthController::class, 'createPassword']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/resend-code', [AuthController::class, 'resendCode']);
});

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
});
