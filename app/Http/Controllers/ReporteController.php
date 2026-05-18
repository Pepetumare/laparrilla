<?php

namespace App\Http\Controllers;

use App\Models\IngresoMercaderia;
use App\Models\Procesamiento;
use App\Models\CierreDiario;
use App\Models\Producto;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $fechaDesde = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $fechaHasta = $request->fecha_hasta ?? now()->format('Y-m-d');

        $productoId = $request->producto_id;
        $sucursalFiltro = $request->sucursal_id;

        if ($user->rol !== 'admin') {
            $sucursalId = $user->sucursal_id;
        } else {
            $sucursalId = $request->filled('sucursal_id')
                ? $sucursalFiltro
                : null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ingresos de mercadería
        |--------------------------------------------------------------------------
        */
        $queryIngresos = IngresoMercaderia::with([
            'proveedor',
            'sucursal',
            'detalles.producto'
        ])
            ->whereBetween('fecha_ingreso', [$fechaDesde, $fechaHasta]);

        if ($sucursalId) {
            $queryIngresos->where('sucursal_id', $sucursalId);
        }

        if ($productoId) {
            $queryIngresos->whereHas('detalles', function ($query) use ($productoId) {
                $query->where('producto_id', $productoId);
            });
        }

        $ingresos = $queryIngresos->get();

        /*
        |--------------------------------------------------------------------------
        | Procesamientos
        |--------------------------------------------------------------------------
        */
        $queryProcesamientos = Procesamiento::with([
            'producto',
            'sucursal',
            'usuario',
            'ingreso.proveedor',
            'detalles'
        ])
            ->whereBetween('fecha_procesamiento', [$fechaDesde, $fechaHasta]);

        if ($sucursalId) {
            $queryProcesamientos->where('sucursal_id', $sucursalId);
        }

        if ($productoId) {
            $queryProcesamientos->where('producto_id', $productoId);
        }

        $procesamientos = $queryProcesamientos->get();

        /*
        |--------------------------------------------------------------------------
        | Cierres diarios
        |--------------------------------------------------------------------------
        */
        $queryCierres = CierreDiario::with([
            'sucursal',
            'usuario',
            'detalles.producto'
        ])
            ->whereBetween('fecha_cierre', [$fechaDesde, $fechaHasta]);

        if ($sucursalId) {
            $queryCierres->where('sucursal_id', $sucursalId);
        }

        if ($productoId) {
            $queryCierres->whereHas('detalles', function ($query) use ($productoId) {
                $query->where('producto_id', $productoId);
            });
        }

        $cierres = $queryCierres->get();

        /*
        |--------------------------------------------------------------------------
        | Totales generales
        |--------------------------------------------------------------------------
        */
        $totalIngresado = $ingresos->sum(function ($ingreso) use ($productoId) {
            $detalles = $ingreso->detalles;

            if ($productoId) {
                $detalles = $detalles->where('producto_id', $productoId);
            }

            return $detalles->sum('peso_kg');
        });

        $totalProcesadoBruto = $procesamientos->sum('peso_inicial_kg');
        $totalUtil = $procesamientos->sum('peso_util_kg');
        $totalMerma = $procesamientos->sum('merma_kg');

        $totalVendido = $cierres->sum(function ($cierre) use ($productoId) {
            $detalles = $cierre->detalles;

            if ($productoId) {
                $detalles = $detalles->where('producto_id', $productoId);
            }

            return $detalles->sum('kilos_vendidos_kg');
        });

        $totalStockRestante = $cierres->sum(function ($cierre) use ($productoId) {
            $detalles = $cierre->detalles;

            if ($productoId) {
                $detalles = $detalles->where('producto_id', $productoId);
            }

            return $detalles->sum('stock_restante_calculado_kg');
        });

        $porcentajeMerma = $totalProcesadoBruto > 0
            ? ($totalMerma / $totalProcesadoBruto) * 100
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Reporte por producto
        |--------------------------------------------------------------------------
        */
        $productosReporte = [];

        foreach ($procesamientos->groupBy('producto_id') as $idProducto => $items) {
            $producto = $items->first()->producto;

            $vendidoProducto = $cierres->sum(function ($cierre) use ($idProducto) {
                return $cierre->detalles
                    ->where('producto_id', $idProducto)
                    ->sum('kilos_vendidos_kg');
            });

            $stockActual = $items->sum('peso_util_kg') - $vendidoProducto;

            $productosReporte[] = [
                'producto' => $producto,
                'procesado' => $items->sum('peso_inicial_kg'),
                'util' => $items->sum('peso_util_kg'),
                'merma' => $items->sum('merma_kg'),
                'vendido' => $vendidoProducto,
                'stock_actual' => max($stockActual, 0),
                'porcentaje_merma' => $items->sum('peso_inicial_kg') > 0
                    ? ($items->sum('merma_kg') / $items->sum('peso_inicial_kg')) * 100
                    : 0,
            ];
        }

        $productos = Producto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $sucursales = $user->rol === 'admin'
            ? Sucursal::where('activo', true)->orderBy('nombre')->get()
            : collect();

        return view('reportes.index', compact(
            'fechaDesde',
            'fechaHasta',
            'productoId',
            'sucursalId',
            'productos',
            'sucursales',
            'ingresos',
            'procesamientos',
            'cierres',
            'totalIngresado',
            'totalProcesadoBruto',
            'totalUtil',
            'totalMerma',
            'totalVendido',
            'totalStockRestante',
            'porcentajeMerma',
            'productosReporte'
        ));
    }

    public function exportarCsv(Request $request)
    {
        $user = auth()->user();

        $fechaDesde = $request->fecha_desde ?? now()->startOfMonth()->format('Y-m-d');
        $fechaHasta = $request->fecha_hasta ?? now()->format('Y-m-d');

        $productoId = $request->producto_id;
        $sucursalFiltro = $request->sucursal_id;

        if ($user->rol !== 'admin') {
            $sucursalId = $user->sucursal_id;
        } else {
            $sucursalId = $sucursalFiltro ?: session('sucursal_activa_id');
        }

        $queryProcesamientos = \App\Models\Procesamiento::with([
            'producto',
            'sucursal'
        ])->whereBetween('fecha_procesamiento', [$fechaDesde, $fechaHasta]);

        if ($sucursalId) {
            $queryProcesamientos->where('sucursal_id', $sucursalId);
        }

        if ($productoId) {
            $queryProcesamientos->where('producto_id', $productoId);
        }

        $procesamientos = $queryProcesamientos->get();

        $queryCierres = \App\Models\CierreDiario::with([
            'detalles.producto',
            'sucursal'
        ])->whereBetween('fecha_cierre', [$fechaDesde, $fechaHasta]);

        if ($sucursalId) {
            $queryCierres->where('sucursal_id', $sucursalId);
        }

        if ($productoId) {
            $queryCierres->whereHas('detalles', function ($query) use ($productoId) {
                $query->where('producto_id', $productoId);
            });
        }

        $cierres = $queryCierres->get();

        $productosReporte = [];

        foreach ($procesamientos->groupBy('producto_id') as $idProducto => $items) {
            $producto = $items->first()->producto;
            $sucursal = $items->first()->sucursal;

            $vendidoProducto = $cierres->sum(function ($cierre) use ($idProducto) {
                return $cierre->detalles
                    ->where('producto_id', $idProducto)
                    ->sum('kilos_vendidos_kg');
            });

            $procesado = $items->sum('peso_inicial_kg');
            $util = $items->sum('peso_util_kg');
            $merma = $items->sum('merma_kg');
            $stockActual = $util - $vendidoProducto;

            $productosReporte[] = [
                'sucursal' => $sucursal->nombre ?? 'Sin sucursal',
                'producto' => $producto->nombre ?? 'Producto',
                'categoria' => $producto->categoria ?? '',
                'procesado' => $procesado,
                'util' => $util,
                'merma' => $merma,
                'porcentaje_merma' => $procesado > 0 ? ($merma / $procesado) * 100 : 0,
                'vendido' => $vendidoProducto,
                'stock_actual' => max($stockActual, 0),
            ];
        }

        $nombreArchivo = 'reporte_la_parrilla_' . $fechaDesde . '_al_' . $fechaHasta . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ];

        $callback = function () use ($productosReporte, $fechaDesde, $fechaHasta) {
            $file = fopen('php://output', 'w');

            // BOM para que Excel abra bien tildes y ñ
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Reporte La Parrilla'], ';');
            fputcsv($file, ['Desde', $fechaDesde, 'Hasta', $fechaHasta], ';');
            fputcsv($file, [], ';');

            fputcsv($file, [
                'Sucursal',
                'Producto',
                'Categoría',
                'Procesado kg',
                'Peso útil kg',
                'Merma kg',
                '% Merma',
                'Vendido kg',
                'Stock actual kg',
            ], ';');

            foreach ($productosReporte as $item) {
                fputcsv($file, [
                    $item['sucursal'],
                    $item['producto'],
                    $item['categoria'],
                    number_format($item['procesado'], 2, ',', ''),
                    number_format($item['util'], 2, ',', ''),
                    number_format($item['merma'], 2, ',', ''),
                    number_format($item['porcentaje_merma'], 1, ',', '') . '%',
                    number_format($item['vendido'], 2, ',', ''),
                    number_format($item['stock_actual'], 2, ',', ''),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
