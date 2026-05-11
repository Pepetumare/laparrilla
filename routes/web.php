<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\IngresoMercaderiaController;

Route::get('/', function () {
    return redirect()->route('ingresos.index');
});

Route::resource('productos', ProductoController::class);

Route::resource('proveedores', ProveedorController::class)
    ->parameters(['proveedores' => 'proveedor']);

Route::resource('ingresos', IngresoMercaderiaController::class);