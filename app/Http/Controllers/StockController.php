<?php

namespace App\Http\Controllers;

use App\Models\Procesamiento;
use App\Models\DetalleCierreDiario;

class StockController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $queryProcesamientos = Procesamiento::with([
            'producto',
            'sucursal'
        ]);

        $queryVentas = DetalleCierreDiario::with([
            'producto',
            'cierre.sucursal'
        ]);

        if ($user->rol !== 'admin') {

            $queryProcesamientos->where('sucursal_id', $user->sucursal_id);

            $queryVentas->whereHas('cierre', function ($query) use ($user) {
                $query->where('sucursal_id', $user->sucursal_id);
            });
        } elseif (session('sucursal_activa_id')) {

            $sucursalId = session('sucursal_activa_id');

            $queryProcesamientos->where('sucursal_id', $sucursalId);

            $queryVentas->whereHas('cierre', function ($query) use ($sucursalId) {
                $query->where('sucursal_id', $sucursalId);
            });
        }

        $procesamientos = $queryProcesamientos->get();
        $ventas = $queryVentas->get();

        $ventasPorProducto = $ventas
            ->groupBy('producto_id')
            ->map(function ($items) {
                return $items->sum('kilos_vendidos_kg');
            });

        $stockPorProducto = $procesamientos
            ->groupBy('producto_id')
            ->map(function ($items, $productoId) use ($ventasPorProducto) {

                $producto = $items->first()->producto;
                $sucursal = $items->first()->sucursal;

                $totalUtilProcesado = $items->sum('peso_util_kg');
                $totalProcesadoBruto = $items->sum('peso_inicial_kg');
                $totalMerma = $items->sum('merma_kg');

                $totalVendido = $ventasPorProducto[$productoId] ?? 0;

                $stockActual = $totalUtilProcesado - $totalVendido;

                return [
                    'producto' => $producto,
                    'sucursal' => $sucursal,
                    'kilos_disponibles' => max($stockActual, 0),
                    'total_util_procesado' => $totalUtilProcesado,
                    'total_vendido' => $totalVendido,
                    'total_procesado' => $totalProcesadoBruto,
                    'total_merma' => $totalMerma,
                    'cantidad_procesamientos' => $items->count(),
                ];
            })
            ->filter(function ($item) {
                return $item['kilos_disponibles'] > 0
                    || $item['total_util_procesado'] > 0
                    || $item['total_vendido'] > 0;
            })
            ->sortBy(fn($item) => $item['producto']->nombre ?? '');

        return view('stock.index', compact('stockPorProducto'));
    }
}
