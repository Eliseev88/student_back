<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrgEventsController;
use App\Http\Controllers\UserController;
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

// Users
Route::get('/users', [UserController::class, 'index']);
Route::prefix('/user')->group(function () {
    Route::post('/store', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/{id}', [UserController::class, 'show']);
});

// Events
Route::get('/events', [EventController::class, 'index']);
// Route::get('/event_users/{eventId}', [EventController::class, 'getUsersToEvent']);
Route::prefix('/event')->group(function () {
    Route::get('/{id}', [EventController::class, 'show']);
});

Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

// Public routes
Route::group(['middleware' => ['api']], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/tokenAuth', [UserController::class, 'getUserByToken']);

    // Events
    Route::get('/user_events', [UserController::class, 'getUserEvents']);
    Route::get('/orgs_events', [OrgEventsController::class, 'getOrganizerEvents']);
    Route::delete('/user_events/{eventId}', [UserController::class, 'unsignUserFromEvent']);
    Route::post('/user_events/{eventId}', [UserController::class, 'signUserToEvent']);

    Route::prefix('/event')->group(function () {
        Route::post('', [EventController::class, 'store']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
    });
});
