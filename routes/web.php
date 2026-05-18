<?php

use App\Http\Controllers\ReporteController;
use App\Http\Controllers\IngresoMercaderiaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedorController;
use Illuminate\Http\Request;
use App\Http\Controllers\ProcesamientoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CierreDiarioController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/cambiar-sucursal', function (Request $request) {
    if (auth()->user()->rol !== 'admin') {
        abort(403);
    }

    session(['sucursal_activa_id' => $request->sucursal_id]);

    return back();
})->name('sucursal.cambiar');

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('ingresos.index');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('ingresos', IngresoMercaderiaController::class);

    Route::resource('productos', ProductoController::class);

    Route::resource('proveedores', ProveedorController::class)
        ->parameters(['proveedores' => 'proveedor']);

    Route::get('/procesamientos', [ProcesamientoController::class, 'index'])
        ->name('procesamientos.index');

    Route::get('/procesamientos/create', [ProcesamientoController::class, 'create'])
        ->name('procesamientos.create');

    Route::post('/procesamientos', [ProcesamientoController::class, 'store'])
        ->name('procesamientos.store');
    Route::get('/stock', [StockController::class, 'index'])
        ->name('stock.index');

    Route::get('/cierres', [CierreDiarioController::class, 'index'])
        ->name('cierres.index');

    Route::get('/cierres/create', [CierreDiarioController::class, 'create'])
        ->name('cierres.create');

    Route::post('/cierres', [CierreDiarioController::class, 'store'])
        ->name('cierres.store');

    Route::get('/reportes', [ReporteController::class, 'index'])
        ->name('reportes.index');

    Route::get('/reportes/exportar-csv', [ReporteController::class, 'exportarCsv'])
        ->name('reportes.exportarCsv');

    Route::get('/reportes/exportar-pdf', [ReporteController::class, 'exportarPdf'])
    ->name('reportes.exportarPdf');
    
});

require __DIR__ . '/auth.php';
