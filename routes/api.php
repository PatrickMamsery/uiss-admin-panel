<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ClubController;
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
    Route::prefix('gallery')->middleware('withoutlink')->group(function () {

        // Images
        Route::get('/images', [GalleryController::class, 'getImages']);
        Route::get('/images/{id}', [GalleryController::class, 'getImage']);
        Route::post('/images', [GalleryController::class, 'createImage']);
        Route::put('/images/{id}', [GalleryController::class, 'updateImage']);
        Route::delete('/images/{id}', [GalleryController::class, 'deleteImage']);

        // Albums
        Route::get('/albums', [GalleryController::class, 'getAlbums']);
        Route::get('/albums/{id}', [GalleryController::class, 'getAlbum']);
        Route::get('/albums/{id}/images', [GalleryController::class, 'getAlbumImages']);
        Route::post('/albums', [GalleryController::class, 'createAlbum']);
        Route::put('/albums/{id}', [GalleryController::class, 'updateAlbum']);
        Route::delete('/albums/{id}', [GalleryController::class, 'deleteAlbum']);
    });

    // Clubs routes
    Route::apiResource('clubs', ClubController::class)->middleware('withoutlink');

    Route::prefix('clubs')->middleware('withoutlink')->group(function () {
        Route::get('/{id}/members', [ClubController::class, 'getClubMembers']);
        Route::get('/{id}/leaders', [ClubController::class, 'getClubLeaders']);
        Route::post('/{id}/addLeader', [ClubController::class, 'addLeader']);

        Route::post('/{id}/addMember', [ClubController::class, 'addMember']);
        Route::post('/{id}/register', [ClubController::class, 'registerToClub']);
    });

    // logout route
    Route::post('/logout', [PassportAuthController::class, 'logout']);

    Route::get('/test', function() {
        return response()->json(['message' => 'This is a test route']);
    });
});
