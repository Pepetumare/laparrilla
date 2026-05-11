<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::with('productos')
            ->latest()
            ->get();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)->get();

        return view('proveedores.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $proveedor = Proveedor::create([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo'),
        ]);

        $proveedor->productos()->sync(
            $request->productos ?? []
        );

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente');
    }

    public function edit(Proveedor $proveedor)
    {
        $productos = Producto::where('activo', true)->get();

        return view('proveedores.edit', compact(
            'proveedor',
            'productos'
        ));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $proveedor->update([
            'nombre' => $request->nombre,
            'telefono' => $request->telefono,
            'activo' => $request->has('activo'),
        ]);

        $proveedor->productos()->sync(
            $request->productos ?? []
        );

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()
            ->route('proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente');
    }
}