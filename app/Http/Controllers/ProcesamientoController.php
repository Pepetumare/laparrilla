<?php

namespace App\Http\Controllers;

use App\Models\IngresoMercaderia;
use App\Models\Procesamiento;
use App\Models\DetalleProcesamiento;
use Illuminate\Http\Request;

class ProcesamientoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Procesamiento::with([
            'producto',
            'sucursal',
            'usuario',
            'detalles',
            'ingreso.proveedor'
        ])->orderByDesc('fecha_procesamiento');

        if ($user->rol !== 'admin') {
            $query->where('sucursal_id', $user->sucursal_id);
        } elseif (session('sucursal_activa_id')) {
            $query->where('sucursal_id', session('sucursal_activa_id'));
        }

        $procesamientos = $query->get();

        return view('procesamientos.index', compact('procesamientos'));
    }

    public function create()
    {
        $user = auth()->user();

        $query = IngresoMercaderia::with([
            'proveedor',
            'detalles.producto',
            'sucursal',
            'procesamientos'
        ])->orderByDesc('fecha_ingreso');

        if ($user->rol !== 'admin') {
            $query->where('sucursal_id', $user->sucursal_id);
        } elseif (session('sucursal_activa_id')) {
            $query->where('sucursal_id', session('sucursal_activa_id'));
        }

        $ingresos = $query->get();

        $ingresosData = $ingresos->mapWithKeys(function ($ingreso) {

            $productos = $ingreso->detalles
                ->groupBy('producto_id')
                ->map(function ($detalles) use ($ingreso) {

                    $productoId = $detalles->first()->producto_id;

                    $totalRecibido = round($detalles->sum('peso_kg'), 2);

                    $totalProcesado = round(
                        $ingreso->procesamientos
                            ->where('producto_id', $productoId)
                            ->sum('peso_inicial_kg'),
                        2
                    );

                    $pendiente = round($totalRecibido - $totalProcesado, 2);

                    return [
                        'producto_id' => $productoId,
                        'producto_nombre' => $detalles->first()->producto->nombre ?? 'Producto',
                        'peso_total' => $totalRecibido,
                        'peso_procesado' => $totalProcesado,
                        'peso_pendiente' => max($pendiente, 0),
                    ];
                })
                ->values()
                ->filter(function ($producto) {
                    return $producto['peso_pendiente'] > 0;
                })
                ->values()
                ->toArray();

            return [
                $ingreso->id => $productos
            ];
        })->toArray();

        return view('procesamientos.create', compact('ingresos', 'ingresosData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ingreso_mercaderia_id' => 'required|exists:ingresos_mercaderia,id',
            'producto_id' => 'required|exists:productos,id',
            'peso_inicial_kg' => 'required|numeric|min:0.01',
            'bolsas' => 'required|array|min:1',
            'bolsas.*.peso_kg' => 'required|numeric|min:0.01',
        ]);

        $ingreso = IngresoMercaderia::findOrFail($request->ingreso_mercaderia_id);

        $ingreso->load([
            'detalles',
            'procesamientos'
        ]);

        $user = auth()->user();

        if ($user->rol !== 'admin' && $ingreso->sucursal_id != $user->sucursal_id) {
            abort(403);
        }

        $totalRecibido = $ingreso->detalles
            ->where('producto_id', $request->producto_id)
            ->sum('peso_kg');

        $totalProcesado = $ingreso->procesamientos
            ->where('producto_id', $request->producto_id)
            ->sum('peso_inicial_kg');

        $pendiente = round($totalRecibido - $totalProcesado, 2);

        $pesoInicial = (float) $request->peso_inicial_kg;

        if ($pesoInicial > $pendiente) {
            return back()
                ->withInput()
                ->with('error', 'No puede procesar más kilos de los pendientes. Pendiente actual: ' . number_format($pendiente, 2) . ' kg.');
        }

        $pesoUtil = collect($request->bolsas)->sum(function ($bolsa) {
            return (float) $bolsa['peso_kg'];
        });

        $merma = $pesoInicial - $pesoUtil;

        if ($merma < 0) {
            return back()
                ->withInput()
                ->with('error', 'El peso útil no puede ser mayor al peso procesado.');
        }

        $procesamiento = Procesamiento::create([
            'ingreso_mercaderia_id' => $ingreso->id,
            'producto_id' => $request->producto_id,
            'sucursal_id' => $ingreso->sucursal_id,
            'user_id' => $user->id,
            'fecha_procesamiento' => now()->format('Y-m-d'),
            'peso_inicial_kg' => $pesoInicial,
            'peso_util_kg' => $pesoUtil,
            'merma_kg' => $merma,
            'observacion' => $request->observacion,
        ]);

        foreach ($request->bolsas as $bolsa) {
            DetalleProcesamiento::create([
                'procesamiento_id' => $procesamiento->id,
                'numero_bolsa' => $bolsa['numero_bolsa'],
                'peso_kg' => $bolsa['peso_kg'],
                'observacion' => $bolsa['observacion'] ?? null,
            ]);
        }

        return redirect()
            ->route('procesamientos.index')
            ->with('success', 'Procesamiento registrado correctamente. Merma calculada automáticamente.');
    }
}
