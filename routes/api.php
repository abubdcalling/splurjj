<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageOrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleReviewController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\VideoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* create by abu sayed (start)*/


// Auth routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
// Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');





Route::post('password/email', [AuthController::class, 'sendResetEmailLink']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');




// //package info bronze(backend) which is namely packages
// Route::middleware('auth:api')->group(function () {

//     Route::post('/packageinfo/bronze', [PackageController::class, 'storeOrUpdateBronze']);
// });


// Route::get('/packageinfo/bronze', [PackageController::class, 'BronzeShow']);

// //package info silver(backend) which is namely packages
// Route::middleware('auth:api')->group(function () {

//     Route::post('/packageinfo/silver', [PackageController::class, 'storeOrUpdateSilver']);
// });

// Route::get('/packageinfo/silver', [PackageController::class, 'SilverShow']);

// //package info gold(backend) which is namely packages
// Route::middleware('auth:api')->group(function () {

//     Route::post('/packageinfo/gold', [PackageController::class, 'storeOrUpdateGold']);
// });

// Route::get('/packageinfo/gold', [PackageController::class, 'goldShow']);


// //package order inserted from frontend which is namely pricing plan
// Route::post('/package-order/{slug}', [PackageOrderController::class, 'store']);

// //notification get from submitted from package orders which is namely notification in backend
// Route::get('/notification', [PackageOrderController::class, 'index'])->middleware('auth:api');


//contact message get from frontend
Route::post('/contactMessage', [ContactMessageController::class, 'store']);


// //packages(backend) which is namely Booking
// Route::middleware('auth:api')->group(function () {
//     Route::get('/package-order-shows', [PackageOrderController::class, 'allShow']);
// });



// //settings(backend) which is namely settings
Route::middleware('auth:api')->group(function () {
    Route::post('settings/email', [SettingController::class, 'updateEmail']);
    Route::put('settings/password', [SettingController::class, 'updatePassword']);
    Route::post('settings/info', [SettingController::class, 'storeOrUpdate']);
    Route::post('settings/logo', [SettingController::class, 'updateLogo']);
    Route::post('settings/profile-pic', [SettingController::class, 'updateProfilePic']);
});

// //blogs (backend) which is namely blogs
// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('blogs', BlogController::class);
// });
// Route::get('/blog-data-front', [BlogController::class, 'getBlogData']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('category/create', [CategoryController::class, 'store']);
// });
// Route::get('category', [CategoryController::class, 'index']);

// //category (backend) which is namely category
// Route::middleware('auth:api')->group(function () {
//     /* Videos Category(start) */
//     Route::get('videos/subcategory', [SubCategoryController::class, 'showSubCatVideos']);
//     Route::post('videos', [SubCategoryController::class, 'createSubCatVideos']);

//     /*Videos Category(end) */

//     // Route::post('ride/create', [CategoryController::class, 'store']);
//     // Route::post('gear/create', [CategoryController::class, 'store']);
//     // Route::post('art-and-culture/create', [CategoryController::class, 'store']);
//     // Route::post('quiet-calm/create', [CategoryController::class, 'store']);
//     // Route::post('latest/create', [CategoryController::class, 'store']);
//     // Route::post('music/create', [CategoryController::class, 'store']);
// });

// Route::get('videos/shows', [CategoryController::class, 'index']);
// Route::post('ride/shows', [CategoryController::class, 'index']);
// Route::post('gear/shows', [CategoryController::class, 'index']);
// Route::post('art-and-culture/shows', [CategoryController::class, 'index']);
// Route::post('quiet-calm/shows', [CategoryController::class, 'index']);
// Route::post('latest/shows', [CategoryController::class, 'index']);
// Route::post('music/shows', [CategoryController::class, 'index']);



// Route::get('/google-reviews', [GoogleReviewController::class, 'getGoogleReviews']);


// Route::middleware('auth:api')->group(function () {
//     Route::put('settings/password', [AuthController::class, 'updatePassword']);
//     Route::post('settings/info', [AuthController::class, 'updateInformation']);
// });

Route::middleware('auth:api')->group(function () {
    
    // Videos API resource

    /*shows all data as subcategory */
    Route::get('videos/{id}', [VideoController::class, 'ShowVideosBySubCategory']);
    Route::apiResource('videos', VideoController::class);


    Route::apiResource('rides', RideController::class);
    Route::apiResource('gears', VideoController::class);
    Route::apiResource('art-and-culture', VideoController::class);
    Route::apiResource('quiet-calim', VideoController::class);
    Route::apiResource('latest', VideoController::class);
    Route::apiResource('music', VideoController::class);

    // Categories API resource
    Route::apiResource('categories', CategoryController::class);

    // Subcategories API resource
    Route::apiResource('subcategories', SubCategoryController::class);
});

// Go to Frontend and Backend API routes
Route::get('videos', [VideoController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('subcategories', [SubCategoryController::class, 'index']);




/* create by abu sayed (end)*/
