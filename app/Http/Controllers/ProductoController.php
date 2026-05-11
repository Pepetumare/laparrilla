<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::latest()->get();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:20',
        ]);

        Producto::create([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'unidad_medida' => $request->unidad_medida,
            'activo' => $request->has('activo'),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto creado correctamente');
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:20',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'categoria' => $request->categoria,
            'unidad_medida' => $request->unidad_medida,
            'activo' => $request->has('activo'),
        ]);

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()
            ->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}