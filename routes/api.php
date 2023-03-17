<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);




Route::middleware([
    'auth:sanctum'
])->group(function () {
    Route::get('/profile', [App\Http\Controllers\API\AuthController::class, 'profile']);
    Route::post('/profile/{id}/updatepassword', [App\Http\Controllers\API\AuthController::class, 'updatepassword']);
    Route::post('/profile/update', [App\Http\Controllers\API\AuthController::class, 'updateProfile']);
    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});


Route::middleware([
    'auth:sanctum',
    'isAdmin',
])->group(function () {
    Route::post('/users/store', [App\Http\Controllers\API\StudentController::class, 'store']);
    Route::get('/users', [App\Http\Controllers\API\StudentController::class, 'index']);
    Route::get('/users/{id}', [App\Http\Controllers\API\StudentController::class, 'show']);
    Route::put('/users/{id}', [App\Http\Controllers\API\StudentController::class, 'update']);
    Route::delete('/users/{id}', [App\Http\Controllers\API\StudentController::class, 'destroy']);
});
