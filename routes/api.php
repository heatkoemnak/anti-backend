<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController;

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

Route::post('register', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

  Route::get('/users', [UserAuthController::class, 'get_all_users']);
  Route::get('/users/{id}', [UserAuthController::class, 'show']);
  Route::put('/users/{id}', [UserAuthController::class, 'update']);
  Route::delete('/users/{id}', [UserAuthController::class, 'destroy']);

// Route::middleware('auth:sanctum')->group(function () {
// });

Route::post('logout', [UserAuthController::class, 'logout'])
  ->middleware('auth:sanctum');
Route::get('user', [UserAuthController::class, 'user'])
  ->middleware('auth:sanctum');
// Route::get('/test', function (Request $request) {
//   return response()->json(['message' => 'API is working!']);
// });

    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::get('products/category/{category}', [ProductController::class, 'getRelatedProductsByCategory']);
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

//   return $request->user();
// });

// Route::apiResource('products', ProductController::class);
Route::apiResource('events', EventController::class);
Route::get('/contacts', [ContactController::class, 'index']);
Route::post('/contacts', [ContactController::class, 'store']);
