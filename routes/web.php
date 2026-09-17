<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/catalogo');
});

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

// Rutas del Carrito de Compras (RF 3)
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::put('/carrito/actualizar/{presentacionId}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
Route::delete('/carrito/eliminar/{presentacionId}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');
Route::delete('/carrito/vaciar', [CarritoController::class, 'vaciar'])->name('carrito.vaciar');
Route::get('/carrito/confirmar', [CarritoController::class, 'confirmar'])->name('carrito.confirmar');

// Rutas de Pedidos / Checkout (RF 3.2 / RF 4)
Route::get('/pedidos/checkout', [PedidoController::class, 'checkout'])->name('pedidos.checkout');
Route::middleware('auth')->group(function () {
    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('pedidos.index');
    Route::get('/mis-pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/confirmar', [PedidoController::class, 'confirmar'])->name('pedidos.confirmar');
    Route::get('/pedidos/{id}/exito', [PedidoController::class, 'exito'])->name('pedido.exito');
});

// Rutas de Autenticación
Route::get('/acceso', [AuthController::class, 'showAcceso'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de Administración
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::resource('productos', ProductoController::class);
});
