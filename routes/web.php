<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController as AdminCompanyController;
use App\Http\Controllers\Admin\TermController as AdminTermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReservationController; 
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CompanyController as MemberCompanyController;
use App\Http\Controllers\TermController as MemberTermController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');
    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);
    Route::resource('restaurants', Admin\RestaurantController::class);
    Route::resource('categories', Admin\CategoryController::class);

    // Company routes
    Route::get('company', [AdminCompanyController::class, 'index'])->name('company.index');
    Route::get('company/{company}/edit', [AdminCompanyController::class, 'edit'])->name('company.edit');
    Route::patch('company/{company}/update', [AdminCompanyController::class, 'update'])->name('company.update');

    // Term routes
    Route::get('terms', [AdminTermController::class, 'index'])->name('terms.index');
    Route::get('terms/{term}/edit', [AdminTermController::class, 'edit'])->name('terms.edit');
    Route::patch('terms/{term}/update', [AdminTermController::class, 'update'])->name('terms.update');
});

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');
});

Route::middleware(['auth', 'verified', 'guest:admin'])->group(function () {
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
    Route::get('subscription/create', [SubscriptionController::class, 'create'])
        ->name('subscription.create')
        ->middleware('not.subscribed');
    
    Route::post('subscription/store', [SubscriptionController::class, 'store'])
        ->name('subscription.store')
        ->middleware('not.subscribed');
    
    Route::get('subscription/edit', [SubscriptionController::class, 'edit'])
        ->name('subscription.edit')
        ->middleware('subscribed');
    
    Route::patch('subscription/update', [SubscriptionController::class, 'update'])
        ->name('subscription.update')
        ->middleware('subscribed');
    
    Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])
        ->name('subscription.cancel')
        ->middleware('subscribed');
    
    Route::delete('subscription/destroy', [SubscriptionController::class, 'destroy'])
        ->name('subscription.destroy')
        ->middleware('subscribed');
        
    
    
    // レストランのレビュー一覧ページのルート
    Route::get('restaurants/{restaurant}/reviews', [ReviewController::class, 'index'])->name('restaurants.reviews.index');
    // レビュー投稿ページのルート
    Route::get('restaurants/{restaurant}/reviews/create', [ReviewController::class, 'create'])->name('restaurants.reviews.create');
    // レビュー投稿処理のルート
    Route::post('restaurants/{restaurant}/reviews', [ReviewController::class, 'store'])->name('restaurants.reviews.store');
    // レビュー編集ページのルート
    Route::get('restaurants/{restaurant}/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('restaurants.reviews.edit');
    // レビュー更新処理のルート
    Route::patch('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'update'])->name('restaurants.reviews.update');
    // レビュー削除処理のルート
    Route::delete('restaurants/{restaurant}/reviews/{review}', [ReviewController::class, 'destroy'])->name('restaurants.reviews.destroy');
    
});

Route::middleware(['auth', 'verified', 'subscribed:premium_plan', 'guest:admin'])->group(function () {
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('restaurants/{restaurant}/reservations/create', [ReservationController::class, 'create'])->name('restaurants.reservations.create');
    Route::post('restaurants/{restaurant}/reservations', [ReservationController::class, 'store'])->name('restaurants.reservations.store');
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

Route::middleware(['auth', 'verified', 'subscribed:premium_plan'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{restaurant_id}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{restaurant_id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
});


Route::middleware(['guest:admin'])->group(function () {
    Route::get('/company', [MemberCompanyController::class, 'index'])->name('company.index');
    Route::get('/terms', [MemberTermController::class, 'index'])->name('terms.index');
});