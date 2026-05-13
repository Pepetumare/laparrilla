<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\IngresoMercaderia;
use App\Models\DetalleIngresoMercaderia;
use App\Models\Sucursal;

class IngresoMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->rol === 'admin') {
            $sucursalActivaId = session('sucursal_activa_id');

            $query = IngresoMercaderia::with([
                'proveedor',
                'detalles.producto',
                'sucursal'
            ]);

            if ($sucursalActivaId) {
                $query->where('sucursal_id', $sucursalActivaId);
            }

            $sucursales = Sucursal::where('activo', true)->get();
        } else {
            $sucursalActivaId = $user->sucursal_id;

            $query = IngresoMercaderia::with([
                'proveedor',
                'detalles.producto',
                'sucursal'
            ])->where('sucursal_id', $user->sucursal_id);

            $sucursales = collect();
        }

        $ingresos = $query
            ->orderByDesc('fecha_ingreso')
            ->get();

        $hoy = now()->format('Y-m-d');

        $ingresosPorDia = $ingresos->groupBy('fecha_ingreso');

        $ingresosHoy = $ingresos->where('fecha_ingreso', $hoy);

        $totalCajasHoy = $ingresosHoy->sum(fn($ingreso) => $ingreso->detalles->count());

        $totalKgHoy = $ingresosHoy->sum(fn($ingreso) => $ingreso->detalles->sum('peso_kg'));

        $totalIngresosHoy = $ingresosHoy->count();

        return view('ingresos.index', compact(
            'ingresosPorDia',
            'totalCajasHoy',
            'totalKgHoy',
            'totalIngresosHoy',
            'sucursales',
            'sucursalActivaId'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();

        return view('ingresos.create', compact('productos', 'proveedores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required',
            'fecha_ingreso' => 'required',
            'producto_id' => 'required',
            'cajas' => 'required|array|min:1',
            'cajas.*.peso_kg' => 'required|numeric|min:0.01',
        ]);

        $user = auth()->user();

        $sucursalId = $user->rol === 'admin'
            ? session('sucursal_activa_id')
            : $user->sucursal_id;

        if (!$sucursalId) {
            return back()
                ->withInput()
                ->with('error', 'Debe seleccionar una sucursal antes de registrar mercadería.');
        }

        $ingreso = IngresoMercaderia::create([
            'proveedor_id' => $request->proveedor_id,
            'sucursal_id' => $sucursalId,
            'fecha_ingreso' => $request->fecha_ingreso,
            'observacion' => null,
        ]);

        foreach ($request->cajas as $caja) {
            DetalleIngresoMercaderia::create([
                'ingreso_mercaderia_id' => $ingreso->id,
                'producto_id' => $request->producto_id,
                'numero_caja' => $caja['numero_caja'],
                'peso_kg' => $caja['peso_kg'],
            ]);
        }

        return redirect()
            ->route('ingresos.index')
            ->with('success', 'Ingreso registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
