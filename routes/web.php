<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController; // Pastikan Anda memiliki controller ini
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminRegisterController; 
use App\Http\Controllers\AboutController;// Added this line to include the DashboardController
use App\Http\Controllers\CartController; // Added this line to include the CartController
use App\Http\Controllers\ReviewController; // Ads line to include the ReviewController

// Route untuk halaman home
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Rute untuk menampilkan daftar produk
Route::get('/produk', [ProdukController::class, 'index'])->name('produk');

// Rute untuk menampilkan detail produk
Route::get('/product/{id}', [ProdukController::class, 'show'])->name('orders');

// Route untuk halaman kontak
Route::get('/kontak', [KontakController::class, 'index'])->name('kontak');
Route::post('/submit-kontak', [KontakController::class, 'store']);
Route::get('/about', [AboutController::class, 'about'])->name('about');

// Route untuk halaman profil
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

// Route untuk melihat profil
Route::get('/profile/view', [ProfileController::class, 'view'])->name('profile.view')->middleware('auth');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::match(['put', 'patch'], 'profile/update', [ProfileController::class, 'update']);

// Route untuk logout
Route::get('/logout', function () {
    request()->session()->invalidate();
    return redirect('/');
})->name('logout');

// Menampilkan form login
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
// Menghandle proses login
Route::post('login', [LoginController::class, 'login']);
// Menghandle proses logout
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Menampilkan form registrasi
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Menghandle proses registrasi
Route::post('register', [RegisterController::class, 'register']);

// Route untuk halaman logout
Route::get('/logout-page', function () {
    return view('logout');
})->name('logout.page');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/view', [ProfileController::class, 'view'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Route untuk menangani pesanan
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

// Route untuk halaman sukses pesanan
Route::get('/orders/success', function () {
    return view('orders.success');
})->name('orders.success');


// Route untuk dashboard admin dengan middleware auth untuk admin
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.create_product');
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.store_product');
    Route::get('/admin/products/edit', [AdminController::class, 'edit'])->name('admin.edit_product');
    Route::put('/admin/products/update', [AdminController::class, 'update'])->name('admin.update_product');
    Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.edit_product');
    Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.update_product');
    Route::delete('/admin/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.delete_product');
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/admin/dashboard/stats', [DashboardController::class, 'stats'])->name('admin.stats'); // Added this line to include the stats route
});
// Tambahkan rute untuk detail order
Route::get('/admin/orders/{order}', [AdminController::class, 'orderDetails'])->name('admin.order_details');

// Route untuk halaman sukses
Route::get('/sukses', function () {
    return view('sukses');
});

// Route untuk edit produk
Route::get('/admin/products/{id}/edit', [ProdukController::class, 'edit'])->name('admin.edit_product');
Route::post('/admin/products/{id}/delete', [ProdukController::class, 'destroy'])->name('admin.delete_product');

Route::get('/admin/manage-products', [ProdukController::class, 'manage'])->name('admin.manage_products');

// Menambahkan rute untuk mengedit pesanan di admin
Route::get('/admin/orders/{order}/edit', [AdminController::class, 'editOrder'])->name('admin.orders_edit');

// Route untuk login admin
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('admin/dashboard', function () {
        return redirect()->route('admin.login');
    })->name('admin.dashboard');
});
Route::delete('/admin/products/{id}/delete', [ProdukController::class, 'deleteProduct'])->name('admin.delete_product');

// Route untuk registrasi admin
Route::get('admin/register', [AdminRegisterController::class, 'showRegistrationForm'])->name('admin.register');
Route::post('admin/register', [AdminRegisterController::class, 'register'])->name('admin.register.submit');

// Added route for confirming orders
Route::post('/admin/orders/{order}/confirm', [AdminController::class, 'confirmOrder'])->name('admin.orders_confirm');


// Route untuk menampilkan halaman keranjang
Route::get('/cart', [CartController::class, 'showCart'])->name('cart');

// Added route for orwders
Route::get('/orders', [OrderController::class, 'index']);

// Definisikan rute untuk membuat review
Route::get('/review/create', [ReviewController::class, 'create'])->name('review.create');

// Definisikan rute untuk menyimpan review
Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');

// Added route for deleting orders
Route::delete('/order/delete/{id}', 'OrderController@delete')->name('order.delete');


Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/carts', [CartController::class, 'showCart'])->name('cart.show');

Route::get('/cart/show/{userId}', [CartController::class, 'showCart'])->name('cart.show');
// routes/web.php
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');

// Rute untuk memperbarui kuantitas produk di keranjang
Route::post('/cart/update', [App\Http\Controllers\CartController::class, 'updateCart']);

// Rute untuk menghapus produk dari keranjang
Route::post('/cart/remove', [App\Http\Controllers\CartController::class, 'removeProductFromCart']);
