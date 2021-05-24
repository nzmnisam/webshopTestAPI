<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriesController;

//AuthController = UserController
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

//Public routes
// Route::resource('products', ProductController::class);

//User register
Route::post('/register', [AuthController::class, 'register']);
//User login
Route::post('/login', [AuthController::class, 'login']);
//Admin register
Route::post('/register/admin', [AdminController::class, 'register']);
//Admin  login
Route::post('/login/admin', [AdminController::class, 'login']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('products/search/{name}', [ProductController::class, 'search']);

//Categories Routes
Route::get('/categories/{slug}', [CategoriesController::class, 'show']);


//Protected routes, needs a token and general user scope
Route::group(['middleware' => ['auth:user','scopes:user']], function() {
    // Route::post('/products', [ProductController::class,'store']);
    // Route::put('/products/{id}', [ProductController::class,'update']);
    // Route::delete('/products/{id}', [ProductController::class,'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);


});
//Protected routes, needs a token and admin scope
Route::group(['middleware' => ['auth:admin','scopes:admin']], function() {
    Route::post('/products', [ProductController::class,'store']);
    Route::put('/products/{id}', [ProductController::class,'update']);
    Route::delete('/products/{id}', [ProductController::class,'destroy']);
    Route::post('/logout/admin', [AdminController::class, 'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
