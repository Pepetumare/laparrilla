<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\IngresoMercaderia;
use App\Models\DetalleIngresoMercaderia;

class IngresoMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hoy = now()->format('Y-m-d');

        $ingresos = IngresoMercaderia::with([
            'proveedor',
            'detalles.producto'
        ])
            ->orderByDesc('fecha_ingreso')
            ->get();

        $ingresosPorDia = $ingresos->groupBy('fecha_ingreso');

        $ingresosHoy = $ingresos->where('fecha_ingreso', $hoy);

        $totalCajasHoy = $ingresosHoy->sum(fn($ingreso) => $ingreso->detalles->count());

        $totalKgHoy = $ingresosHoy->sum(fn($ingreso) => $ingreso->detalles->sum('peso_kg'));

        $totalIngresosHoy = $ingresosHoy->count();

        return view('ingresos.index', compact(
            'ingresosPorDia',
            'totalCajasHoy',
            'totalKgHoy',
            'totalIngresosHoy'
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
        ]);

        $ingreso = IngresoMercaderia::create([
            'proveedor_id' => $request->proveedor_id,
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
