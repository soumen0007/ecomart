<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ContactController;

Route::get('/', [HomeController::class,'index'])->name('home');

Route::get('/product/{id}', [ProductController::class,'show'])->name('product');

Route::get('/login',[AuthController::class,'login'])->name('login');

Route::get('/signup',[AuthController::class,'signup'])->name('signup');

Route::post('/login', [AuthController::class, 'loginSubmit'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/signup', [AuthController::class, 'signupSubmit'])->name('signup.submit');
Route::get('/shopping-list', [ShoppingListController::class, 'index'])
    ->name('shopping')
    ->middleware('auth');

Route::post('/shopping-list/add/{productId}', [ShoppingListController::class, 'add'])
    ->name('shopping.add')
    ->middleware('auth');

Route::post('/shopping-list/remove/{id}', [ShoppingListController::class, 'remove'])
    ->name('shopping.remove')
    ->middleware('auth');

    Route::get('/contact', [ContactController::class,'index'])
    ->name('contact');

Route::post('/contact', [ContactController::class,'submit'])
    ->name('contact.submit');

Route::get('/admin/login', [AdminController::class, 'login'])
    ->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'loginSubmit'])
    ->name('admin.login.submit');

Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard');

Route::post('/admin/logout', [AdminController::class, 'logout'])
    ->name('admin.logout');

Route::get('/admin/products', [AdminController::class, 'products'])
    ->name('admin.products');

Route::get('/admin/products/create', [AdminController::class, 'productCreate'])
    ->name('admin.products.create');

Route::post('/admin/products/store', [AdminController::class, 'productStore'])
    ->name('admin.products.store');

Route::get('/admin/products/edit/{id}', [AdminController::class, 'productEdit'])
    ->name('admin.products.edit');

Route::post('/admin/products/update/{id}', [AdminController::class, 'productUpdate'])
    ->name('admin.products.update');

Route::post('/admin/products/delete/{id}', [AdminController::class, 'productDelete'])
    ->name('admin.products.delete');

Route::get('/admin/categories', [AdminController::class, 'categories'])
    ->name('admin.categories');

Route::get('/admin/users', [AdminController::class, 'users'])
    ->name('admin.users');

Route::get('/admin/messages', [AdminController::class, 'messages'])
    ->name('admin.messages');