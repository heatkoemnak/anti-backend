<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserAuthController;
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

Route::post('register',[UserAuthController::class,'register']);
Route::post('login',[UserAuthController::class,'login']);
Route::post('logout',[UserAuthController::class,'logout'])
  ->middleware('auth:sanctum');
Route::get('user',[UserAuthController::class,'user'])
  ->middleware('auth:sanctum');


  Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserAuthController::class, 'get_all_users']);
    Route::get('/users/{id}', [UserAuthController::class, 'show']);
    Route::put('/users/{id}', [UserAuthController::class, 'update']);
    Route::delete('/users/{id}', [UserAuthController::class, 'destroy']);
});


Route::get('/test', function (Request $request) {
    return response()->json(['message' => 'API is working!']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
