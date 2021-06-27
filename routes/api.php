<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\KupujeController;
use App\Http\Controllers\MestosController;
use App\Http\Controllers\ModelsController;

use App\Models\Images;

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
//Products routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/category/{category_id}', [ProductController::class, 'showByCategory']);
Route::get('/products/categories/{categories_id}', [ProductController::class, 'showByCategories']);
Route::get('/products/similar/{product_id}', [ProductController::class, 'showSimilar']);


Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/products/id/{id}', [ProductController::class, 'showById']);
Route::get('products/search/{name}', [ProductController::class, 'search']);

//City routes
Route::get('/cities', [MestosController::class, 'index']);

//Categories Routes
Route::get('/categories/{slug}', [CategoriesController::class, 'show']);
Route::get('/categories/all/all', [CategoriesController::class, 'showAll']);
Route::get('/categories/subCategories/{slug}', [CategoriesController::class, 'showSubCategories']);
Route::get('/categories/ancestors/{category_id}', [CategoriesController::class, 'ancestors']);


//Images routes
Route::get('/images', [ImagesController::class, 'index']);
Route::get('/thumbnails', [ImagesController::class, 'getThumbnails']);
Route::get('/thumbnails/{productIds}', [ImagesController::class, 'getThumbnailsForSimilar']);
Route::get('/images/{product_id}', [ImagesController::class, 'getImagesForProduct']);


//Model routes
Route::get('/models/{product_id}', [ModelsController::class, 'getModelForProduct']);



//Premestiti u scope za usera



//Premestiti u scope za admina

//Napraviti zajednicki scope koji prihvata oba tokena
Route::post('/cart', [CartController::class, 'store']);
Route::post('/cart/many', [CartController::class, 'deleteFromCart']);
Route::post('/cart/one', [CartController::class, 'deleteOneFromCart']);
Route::post('/cart/all', [CartController::class, 'deleteAllFromCart']);


//Protected routes, needs a token and general user scope
Route::group(['middleware' => ['auth:user','scopes:user']], function() {
    Route::post('/logout', [AuthController::class, 'logout']);
    //kupovina
    //na frontu uraditi da user moze da brise svoju istoriju kupovine
    Route::get('/kupuje', [KupujeController::class, 'index']); 
    Route::post('/kupuje', [KupujeController::class, 'store']);
    Route::get('/kupuje/product/{id}', [KupujeController::class, 'showByProduct']);
    //Cart
    Route::get('/cart/user/{id}', [CartController::class, 'getCartForUser']);





});
//Protected routes, needs a token and admin scope

Route::delete('/products/{id}', [ProductController::class,'destroy']);

Route::group(['middleware' => ['auth:admin','scopes:admin']], function() {
    //Product routes
    Route::post('/products', [ProductController::class,'store']);
    Route::put('/products/{id}', [ProductController::class,'update']);
    Route::delete('/products/{id}', [ProductController::class,'destroy']);
    //Store product images
    Route::post('/images', [ImagesController::class, 'store']);
    Route::put('/images/product_id', [ImagesController::class, 'setProductId']);
    Route::put('/images/product_id/update', [ImagesController::class, 'setProductIdUpdate']);

    //Delete images
    Route::delete('/images/{id}', [ImagesController::class, 'destroy']);

    //Store product model
    Route::post('/models', [ModelsController::class, 'store']);
    Route::put('/models/product_id/{id}', [ModelsController::class, 'setProductId']);
    //Delete images
    Route::delete('/models/{id}', [ModelsController::class, 'destroy']);


    //kupovina
    Route::get('/kupuje/user/{id}', [KupujeController::class, 'showByUser']);
    Route::delete('/kupuje/deleteByUser/{id}', [KupujeController::class, 'deleteByUser']);
    Route::delete('/kupuje/deleteByProduct/{id}', [KupujeController::class, 'deleteByProduct']);
    Route::post('/kupuje/delete', [KupujeController::class, 'delete']);
    //cart
    Route::get('/cart/admin/{id}', [CartController::class, 'getCartForAdmin']);


    //Logout route
    Route::post('/logout/admin', [AdminController::class, 'logout']);
});



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
