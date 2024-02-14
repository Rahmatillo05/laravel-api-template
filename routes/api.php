<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\v1\OrganizationController;
use App\Models\SmsLog;
use App\Models\User;
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
Route::post('sms-log', function (Request $request) {
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

Route::prefix('organizations')
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/', [OrganizationController::class, 'store']);
        Route::middleware(['auth:api', 'scope:' . User::ROLE_ORGANIZATION])->group(function () {
            Route::get('/my-organization', [OrganizationController::class, 'myOrganization'])->middleware(['auth:api', 'scope:' . User::ROLE_ORGANIZATION]);
            Route::put('/{organization}', [OrganizationController::class, 'update'])->whereNumber('organization');
            Route::post('/details', [OrganizationController::class, 'details']);
            Route::get('/branches', [OrganizationController::class, 'branches']);
            Route::post('/branches', [OrganizationController::class, 'storeBranch']);
            Route::put('/branches/{organization}', [OrganizationController::class, 'updateBranch'])->whereNumber('organization');
            Route::delete('/branches/{organization}', [OrganizationController::class, 'destroyBranch'])->whereNumber('organization');
        });
    });
