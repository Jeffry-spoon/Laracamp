<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\CheckoutController as AdminCheckout;
use App\Http\Controllers\Admin\DiscountController as AdminDiscount;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})-> name('welcome');

// socialite routes
Route::get('sign-in-google', [UserController::class, 'google'])->name('user.login.google');
Route::get('auth/google/callback', [UserController::class, 'handleProviderCallback'])->name('user.google.callback');

// midtrans routes
Route::get('payment/success', [UserController::class,'midtransCallback']);
Route::get('payment/success', [CheckoutController::class, 'midtransCallback']);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Checkout routes
    Route::get('checkout/success', [CheckoutController::class, 'success_checkout'])->name('checkout.success')->middleware('ensureUserRole:user');
    Route::get('checkout/{camp:slug}', [CheckoutController::class, 'create'])->name('checkout.create')->middleware('ensureUserRole:user');
    Route::post('checkout/{camp}', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('ensureUserRole:user');

    // Dashboard routes
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    // user Dashboard
    Route::prefix('user/dasboard')->namespace('User.')->name('user.')->middleware('ensureUserRole:user')->group(function() {
        Route::get('/', [UserDashboard::class, 'index'])->name('dashboard');
    });

     // admin Dashboard
     Route::prefix('admin/dasboard')->name('admin.')->middleware('ensureUserRole:admin')->group(function() {
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // admin checkout
        Route::post('checkout/{checkout}', [AdminCheckout::class, 'update'])->name('checkout.update');

        // admin discount
        Route::resource('discount', AdminDiscount::class);
    });
});

// Route::get('dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
