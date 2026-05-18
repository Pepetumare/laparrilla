<?php

namespace App\Http\Controllers;

use App\Models\CierreDiario;
use App\Models\DetalleCierreDiario;
use App\Models\Procesamiento;
use App\Models\Sucursal;
use Illuminate\Http\Request;


class CierreDiarioController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = CierreDiario::with([
            'sucursal',
            'usuario',
            'detalles.producto'
        ])->orderByDesc('fecha_cierre');

        if ($user->rol !== 'admin') {
            $query->where('sucursal_id', $user->sucursal_id);
        } elseif (session('sucursal_activa_id')) {
            $query->where('sucursal_id', session('sucursal_activa_id'));
        }

        $cierres = $query->get();

        return view('cierres.index', compact('cierres'));
    }

    public function create()
    {
        $user = auth()->user();

        $sucursalId = $user->rol === 'admin'
            ? session('sucursal_activa_id')
            : $user->sucursal_id;

        if (!$sucursalId) {
            return redirect()
                ->route('cierres.index')
                ->with('error', 'Debe seleccionar una sucursal antes de crear un cierre diario.');
        }

        $fecha = now()->format('Y-m-d');

        $cierreExistente = CierreDiario::where('sucursal_id', $sucursalId)
            ->where('fecha_cierre', $fecha)
            ->first();

        if ($cierreExistente) {
            return redirect()
                ->route('cierres.index')
                ->with('error', 'Ya existe un cierre diario para esta sucursal en la fecha de hoy.');
        }

        $procesamientos = Procesamiento::with('producto')
            ->where('sucursal_id', $sucursalId)
            ->get();

        $ventas = DetalleCierreDiario::whereHas('cierre', function ($query) use ($sucursalId) {
            $query->where('sucursal_id', $sucursalId);
        })
            ->get();

        $ventasPorProducto = $ventas
            ->groupBy('producto_id')
            ->map(function ($items) {
                return $items->sum('kilos_vendidos_kg');
            });

        $stockPorProducto = $procesamientos
            ->groupBy('producto_id')
            ->map(function ($items, $productoId) use ($ventasPorProducto) {
                $producto = $items->first()->producto;

                $pesoUtil = $items->sum('peso_util_kg');
                $vendido = $ventasPorProducto[$productoId] ?? 0;

                $stockActual = $pesoUtil - $vendido;

                return [
                    'producto' => $producto,
                    'stock_disponible_kg' => round(max($stockActual, 0), 2),
                ];
            })
            ->filter(function ($stock) {
                return $stock['stock_disponible_kg'] > 0;
            })
            ->sortBy(fn($stock) => $stock['producto']->nombre ?? '');
        $sucursal = Sucursal::find($sucursalId);

        return view('cierres.create', compact(
            'stockPorProducto',
            'sucursal',
            'fecha'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_cierre' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.stock_disponible_kg' => 'required|numeric|min:0',
            'productos.*.kilos_vendidos_kg' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        $sucursalId = $user->rol === 'admin'
            ? session('sucursal_activa_id')
            : $user->sucursal_id;

        if (!$sucursalId) {
            return back()
                ->withInput()
                ->with('error', 'Debe seleccionar una sucursal antes de guardar el cierre.');
        }

        $existe = CierreDiario::where('sucursal_id', $sucursalId)
            ->where('fecha_cierre', $request->fecha_cierre)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe un cierre diario para esta sucursal en esta fecha.');
        }

        $cierre = CierreDiario::create([
            'sucursal_id' => $sucursalId,
            'user_id' => $user->id,
            'fecha_cierre' => $request->fecha_cierre,
            'observacion' => $request->observacion,
        ]);

        foreach ($request->productos as $producto) {
            $stockDisponible = (float) $producto['stock_disponible_kg'];
            $kilosVendidos = (float) $producto['kilos_vendidos_kg'];

            if ($kilosVendidos > $stockDisponible) {
                return back()
                    ->withInput()
                    ->with('error', 'Los kilos vendidos no pueden ser mayores al stock disponible.');
            }

            $stockRestante = $stockDisponible - $kilosVendidos;

            DetalleCierreDiario::create([
                'cierre_diario_id' => $cierre->id,
                'producto_id' => $producto['producto_id'],
                'stock_disponible_kg' => $stockDisponible,
                'kilos_vendidos_kg' => $kilosVendidos,
                'stock_restante_calculado_kg' => $stockRestante,
                'observacion' => $producto['observacion'] ?? null,
            ]);
        }

        return redirect()
            ->route('cierres.index')
            ->with('success', 'Cierre diario registrado correctamente.');
    }
}
