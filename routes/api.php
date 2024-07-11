<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WasteController;

use App\Http\Controllers\CartController;

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
Route::get('/test', function (Request $request) {
  return response()->json(['message' => 'API is working!']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::apiResource('/products', ProductController::class);
Route::apiResource('/events', EventController::class);
// CONTACT
Route::get('/contacts', [ContactController::class, 'index']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::put('/contacts/{id}', [ContactController::class, 'update']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
Route::apiResource('/wastes', WasteController::class);

// extra - rate & comment on product
Route::post('/products/{id}/rate', [ProductController::class, 'rateProduct']);
Route::post('/products/{id}/comment', [ProductController::class, 'commentProduct']);
Route::post('/cart/add', 'CartController@addToCart');

//add to card

Route::middleware('auth')->group(function () {
  Route::get('/cart-items', [CartController::class, 'index']);
  Route::post('/cart-items', [CartController::class, 'store']);
  Route::get('/cart-items/{id}', [CartController::class, 'show']);
  Route::put('/cart-items/{id}', [CartController::class, 'update']);
  Route::delete('/cart-items/{id}', [CartController::class, 'destroy']);
});
