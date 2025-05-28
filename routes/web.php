<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InicioImagenesController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PrecompraController;
use App\Models\Producto;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\FacturaController;

//creados no tocar
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $productos = Producto::with(['categoria', 'proveedor', 'color'])->get();
        return Inertia::render('Dashboard',['productos' => $productos]);
    })->name('dashboard');
});
Route::get('/nosotros', function () {
    return Inertia::render('Nosotros');
})->name('nosotros');
//creados si se puede tocar //imgenes de carrusel 
Route::get('/inicio-imagenes', [InicioImagenesController::class, 'getImages']);

//google inciar session
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

//categorias inicio carusel
Route::get('/categorias', [CategoriaController::class, 'index']);
Route::get('/categorias/{id}', [CategoriaController::class, 'show'])->name('categorias.show');
Route::get('/productos', [ProductoController::class, 'index']);
//precompra
Route::get('/precompra/{id}', [PrecompraController::class, 'show'])->name('precompra.show');
//carrito
Route::post('/carrito/{id}', [CarritoController::class, 'agregarAlCarrito'])->name('carrito.agregar');
Route::get('/carrito', [CarritoController::class, 'verCarrito'])->name('carrito.ver');
Route::delete('/carrito/eliminar/{id}', [CarritoController::class, 'eliminarDelCarrito'])->name('carrito.eliminar');
Route::post('/carrito/actualizar/{id}', [CarritoController::class, 'actualizarCantidad'])->name('carrito.actualizar');
//nuevo
Route::post('/validar-productos-stock', [CarritoController::class, 'validarStock']);
//cupon
Route::post('/validar-cupon', [CarritoController::class, 'validar'])->name('validar-cupon');
//venta
Route::get('/ventas', [VentaController::class, 'verVentas'])->name('ventas.ver');
Route::post('/comprobantes/guardar', [VentaController::class, 'guardar'])->name('comprobantes.guardar');
//soporte
Route::get('/soporte', [SoporteController::class, 'index'])->name('soporte.index');
Route::put('/soporte/{id}', [SoporteController::class, 'update'])->name('soporte.update');
Route::post('/soporte', [SoporteController::class, 'store'])->name('soporte.store');
Route::delete('/soporte/{id}', [SoporteController::class, 'destroy'])->name('soporte.destroy');
//factura
Route::get('/factura/{id}', [FacturaController::class, 'generarFactura']);
//factura filament
Route::get('/ventas/{id}/generar-factura', [FacturaController::class, 'generarFacturaFilament'])->name('ventas.generarFactura');