<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\HomeController as AdminHomeController;  
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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
});
Route::middleware(['auth', 'verified', 'guest:admin'])->group(function () {
    Route::resource('user', UserController::class)->only(['index', 'edit', 'update']);
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



