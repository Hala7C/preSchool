<?php

use App\Http\Controllers\API\ClassController;
use App\Http\Controllers\API\FeesStudentController;
use App\Http\Controllers\API\LevelController;
use App\Http\Controllers\API\SubjectController as APISubjectController;
use App\Http\Controllers\SubjectController;
use App\Models\FeesConfig;
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
    Route::post('/profile/{id}/update', [App\Http\Controllers\API\AuthController::class, 'updateProfile']);
    //  Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
});
Route::middleware(['isManager'])->group(function () {
    Route::apiResource('classes',  App\Http\Controllers\API\ClassController::class);
    Route::apiResource('levels',   App\Http\Controllers\API\LevelController::class);
    Route::apiResource('subject',  App\Http\Controllers\API\SubjectController::class);
    Route::apiResource('config',   App\Http\Controllers\API\FeesStudentController::class);
});
