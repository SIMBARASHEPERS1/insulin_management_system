<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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

// Support us
Volt::route('/support-us', 'support-us');

// Login
Volt::route('/login', 'login')->name('login');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Volt::route('/', 'dashboard.index')
        ->name('dashboard');

    //Logout
    Route::get('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    });

    // Users
    Volt::route('/patients/view', 'users.index');
    Volt::route('/patient/{user}/edit', 'users.edit');
    Volt::route('/patient/create', 'users.create');
    Volt::route('/patient/{user}', 'users.show');
    Volt::route('/patient/{user}/entry', 'users.entries');

    // Brands
    Volt::route('/brands', 'brands.index');
    Volt::route('/brands/{brand}/edit', 'brands.edit');
    Volt::route('/brands/create', 'brands.create');

    // Categories
    Volt::route('/categories', 'categories.index');
    Volt::route('/categories/{category}/edit', 'categories.edit');
    Volt::route('/categories/create', 'categories.create');

    // Products
    Volt::route('/products', 'products.index');
    Volt::route('/products/{product}/edit', 'products.edit');
    Volt::route('/products/create', 'products.create');

    // Orders
    Volt::route('/orders', 'orders.index')->middleware('is.patient');
    Volt::route('/orders/{order}/edit', 'orders.edit')->middleware('is.patient');
    Volt::route('/orders/create', 'orders.create')->middleware('is.patient');

});
