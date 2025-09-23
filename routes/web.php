<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\ApartadoController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProvidersController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function() {
   
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::get('/register', [RegisterController::class,'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class,'register']);

    
    Route::get('/password/reset', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class,'reset'])->name('password.update');
});


Route::middleware('auth')->group(function() {
    
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');

    
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    
    Route::post('/cart/add/{id}', [DashboardController::class,'agregarAlCarrito'])->name('cart.add');
    Route::post('/cart/remove/{id}', [DashboardController::class,'removerDelCarrito'])->name('cart.remove');
    Route::post('/cart/update/{id}', [DashboardController::class,'actualizarCarrito'])->name('cart.update');
    Route::post('/cart/clear', [DashboardController::class,'limpiarCarrito'])->name('cart.clear');
    Route::post('/cart/finalize', [DashboardController::class,'finalizarVenta'])->name('cart.finalize');
    Route::post('/cart/apartado', [DashboardController::class,'crearApartado'])->name('cart.apartado');

    
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

   
    Route::get('/statistics', [StatisticsController::class,'index'])->name('statistics.index');

    
    Route::get('/inventory', [InventoryController::class,'index'])->name('inventory.index');
    Route::post('/inventory', [InventoryController::class,'store'])->name('inventory.store');

    
    Route::get('/providers', [ProvidersController::class,'index'])->name('providers.index');
    Route::post('/providers', [ProvidersController::class,'store'])->name('providers.store');

    
    Route::get('/apartados', [ApartadoController::class,'index'])->name('apartados.index');
    Route::post('/apartados/create', [ApartadoController::class,'store'])->name('apartados.store');

    
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
});
