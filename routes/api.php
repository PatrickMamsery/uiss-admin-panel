<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PassportAuthController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\UserController;

use App\Http\Middleware\WithoutLinks;

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

Route::post('/register', [PassportAuthController::class, 'register']);
Route::post('/login', [PassportAuthController::class, 'login']);

Route::middleware(['auth.api'])->group(function () {
    Route::apiResource('users', UserController::class)->middleware('withoutlink');
    // Route::apiResource('users', UserController::class);
    Route::apiResource('programs', ProgramController::class)->middleware('withoutlink');
    Route::apiResource('projects', ProjectController::class)->middleware('withoutlink');
    Route::apiResource('events', EventController::class)->middleware('withoutlink');
    Route::post('/logout', [PassportAuthController::class, 'logout']);
});
