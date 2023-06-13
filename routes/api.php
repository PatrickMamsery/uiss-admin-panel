<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\MemberController;
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

// added this so as to deal with CORS-ORIGIN errors
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [PassportAuthController::class, 'register']);
Route::post('/login', [PassportAuthController::class, 'login']);

Route::middleware(['auth.api'])->group(function () {
    Route::apiResource('users', UserController::class)->except('store')->middleware('withoutlink');
    Route::post('users', [UserController::class, 'store']);

    // Route::get('leaders', [MemberController::class, 'getLeaders']);

    Route::get('users/role/{id}', [MemberController::class, 'getUsersByRole']);


    // Program routes
    Route::apiResource('programs', ProgramController::class)->middleware('withoutlink');

    // Project routes
    Route::apiResource('projects', ProjectController::class)->middleware('withoutlink');

    // Event routes
    Route::apiResource('events', EventController::class)->middleware('withoutlink');
    Route::post('events/{id}/register', [EventController::class, 'registerToEvent'])->middleware('withoutlink');
    Route::get('events/{id}/registration-list', [EventController::class, 'getRegisteredUsers'])->middleware('withoutlink');
    // Route::post('events/{id}/unregister', [EventController::class, 'unregisterFromEvent'])->middleware('withoutlink');

    // Gallery routes
    // Images
    Route::get('/gallery/images', [GalleryController::class, 'getImages'])->middleware('withoutlink');
    Route::get('/gallery/images/{id}', [GalleryController::class, 'getImage'])->middleware('withoutlink');
    Route::post('/gallery/images', [GalleryController::class, 'createImage'])->middleware('withoutlink');
    Route::put('/gallery/images/{id}', [GalleryController::class, 'updateImage'])->middleware('withoutlink');
    Route::delete('/gallery/images/{id}', [GalleryController::class, 'deleteImage'])->middleware('withoutlink');

    // Albums
    Route::get('/gallery/albums', [GalleryController::class, 'getAlbums'])->middleware('withoutlink');
    Route::get('/gallery/albums/{id}', [GalleryController::class, 'getAlbum'])->middleware('withoutlink');
    Route::get('/gallery/albums/{id}/images', [GalleryController::class, 'getAlbumImages'])->middleware('withoutlink');
    Route::post('/gallery/albums', [GalleryController::class, 'createAlbum'])->middleware('withoutlink');
    Route::put('/gallery/albums/{id}', [GalleryController::class, 'updateAlbum'])->middleware('withoutlink');
    Route::delete('/gallery/albums/{id}', [GalleryController::class, 'deleteAlbum'])->middleware('withoutlink');

    // logout route
    Route::post('/logout', [PassportAuthController::class, 'logout']);

    Route::get('/test', function() {
        return response()->json(['message' => 'This is a test route']);
    });
});
