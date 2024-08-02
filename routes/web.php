<?php

use App\Http\Controllers\ScaleController;
use App\Livewire\CategoriaController;
use App\Livewire\ClienteController;
use App\Livewire\DashboardController;
use App\Livewire\MostrarVentas;
use App\Livewire\ProductoController;
use App\Livewire\ProveedorController;
use App\Livewire\UsuarioController;
use App\Livewire\VentaController;
use Illuminate\Support\Facades\Route;


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

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get("/dashboard", DashboardController::class)->name("dashboard");

    Route::view('profile', 'profile')->name('profile');

    Route::get('/productos', ProductoController::class)->name("productos");
    Route::get('/productos/pdf', [ProductoController::class, "generarPDFProductos"])->name("generarPDFProductos");
    Route::get('/productos/excel', [ProductoController::class, "generarExcelProductos"])->name("generarExcelProductos");
    Route::get('/usuarios', UsuarioController::class)->name("usuarios");
    Route::get('/usuarios/pdf', [UsuarioController::class, "generarPDFUsuarios"])->name("generarPDFUsuarios");
    Route::get('/usuarios/excel', [UsuarioController::class, "generarExcelUsuarios"])->name("generarExcelUsuarios");
    Route::get('/modulo-pos', VentaController::class)->name("moduloPOS");
    Route::get('/ventas', MostrarVentas::class)->name("ventas");
    Route::get('/recibo-ventas', [MostrarVentas::class, "generarPDFVentas"])->name("generarPDFVentas");
    Route::get('/recibo-venta/{venta}', [VentaController::class, "generarPDFRecibo"])->name("generarPDFRecibo");
    Route::get('/recibo-ventas/{ventas}', [MostrarVentas::class, "generarPDFRecibos"])->name("generarPDFRecibos");
    Route::get('/recibo-ultima-venta', [VentaController::class, "imprimirUltimaVenta"])->name("imprimirUltimaVenta");
    Route::get('/clientes', ClienteController::class)->name("clientes");
    Route::get('/clientes/pdf', [ClienteController::class, "generarPDFClientes"])->name("generarPDFClientes");
    Route::get('/clientes/excel', [ClienteController::class, "generarExcelClientes"])->name("generarExcelClientes");
    Route::get('/categorias', CategoriaController::class)->name("categorias");
    Route::get('/categorias/pdf', [CategoriaController::class, "generarPDFCategorias"])->name("generarPDFCategorias");
    Route::get('/categorias/excel', [CategoriaController::class, "generarExcelCategorias"])->name("generarExcelCategorias");
    Route::get('/proveedores', ProveedorController::class)->name("proveedores");
    Route::get('/proveedores/excel', [ProveedorController::class, "generarExcelProveedores"])->name("generarExcelProveedores");
    Route::get('/recibo-proveedores', [ProveedorController::class, "generarPDFProveedores"])->name("generarPDFProveedores");
    Route::get('/imprimir-venta', [VentaController::class, "generarReciboTermico"])->name("generarReciboTermico");
    Route::get('/scale/read', [ScaleController::class, 'index']);
});

require __DIR__ . '/auth.php';
