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
Volt::route('/support-us', 'support-us')
    ->middleware('guest');
// Login
Volt::route('/login', 'login')
    ->middleware('guest')
    ->name('login');

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
    Volt::route('/patients/view', 'patients.index');
    Volt::route('/patient/{user}/edit', 'patients.edit');
    Volt::route('/patient/create', 'patients.create');
    Volt::route('/patient/{user}', 'patients.show');
    Volt::route('/patient/{user}/entry', 'patients.entries');

    // Analytics and reports
    Volt::route('/reports', 'analytics.index');
    Volt::route('/analytics/{category}/edit', 'analytics.edit');
    Volt::route('/analytics/create', 'analytics.create');

    // user settings
    Volt::route('/userSettings', 'userSettings.index');
    Volt::route('/userSettings/{brand}/edit', 'userSettings.edit');
    Volt::route('/userSettings/create', 'userSettings.create');

    // Products
    Volt::route('/products', 'products.index');
    Volt::route('/products/{product}/edit', 'products.edit');
    Volt::route('/products/create', 'products.create');

    // Orders
    Volt::route('/orders', 'orders.index')->middleware('is.patient');
    Volt::route('/orders/{order}/edit', 'orders.edit')->middleware('is.patient');
    Volt::route('/orders/create', 'orders.create')->middleware('is.patient');

});
