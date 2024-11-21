<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\NotSubscribed;
use Illuminate\Http\Request;
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

require __DIR__.'/auth.php';

Route::group(['middleware' => 'guest:admin'], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update'])->middleware(['auth', 'verified'])->names('user');
    Route::resource('restaurants', RestaurantController::class)->only(['index', 'show'])->names('restaurants');
   });
   
 //User
 Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
  
    Route::group(['middleware' => [NotSubscribed::class]], function () {
        Route::get('subscription/create', [SubscriptionController::class, 'create'])->name('subscription.create');
        Route::post('subscription', [SubscriptionController::class, 'store'])->name('subscription.store');
    });

    Route::group(['middleware' => [Subscribed::class]], function () {
        Route::get('subscription/edit', [SubscriptionController::class, 'edit'])->name('subscription.edit');
        Route::patch('subscription', [SubscriptionController::class, 'update'])->name('subscription.update');
        Route::get('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
        Route::delete('subscription', [SubscriptionController::class, 'destroy'])->name('subscription.destroy');
    });
 });
Route::get('company', [CompanyController::class, 'index'])->name('company.index');
Route::get('terms', [TermController::class, 'index'])->name('terms.index');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [AdminHomeController::class, 'index'])->name('home');
    Route::resource('users', AdminUserController::class)->only(['index', 'show']);
    Route::resource('restaurants', AdminRestaurantController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('company', AdminCompanyController::class)->only(['index', 'edit', 'update']);
    Route::resource('terms', AdminTermController::class)->only(['index', 'edit', 'update']);
});



