@extends('layouts.app')

@section('content')

<h2 class="mb-4">Editar producto</h2>

<form action="{{ route('productos.update', $producto) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Nombre del producto</label>
                <input type="text" name="nombre" class="form-control" value="{{ $producto->nombre }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select" required>
                    <option value="Cerdo" {{ $producto->categoria == 'Cerdo' ? 'selected' : '' }}>Cerdo</option>
                    <option value="Vacuno" {{ $producto->categoria == 'Vacuno' ? 'selected' : '' }}>Vacuno</option>
                    <option value="Pollo" {{ $producto->categoria == 'Pollo' ? 'selected' : '' }}>Pollo</option>
                    <option value="Otros" {{ $producto->categoria == 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Unidad de medida</label>
                <select name="unidad_medida" class="form-select" required>
                    <option value="kg" {{ $producto->unidad_medida == 'kg' ? 'selected' : '' }}>Kg</option>
                    <option value="unidad" {{ $producto->unidad_medida == 'unidad' ? 'selected' : '' }}>Unidad</option>
                    <option value="caja" {{ $producto->unidad_medida == 'caja' ? 'selected' : '' }}>Caja</option>
                </select>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="activo" {{ $producto->activo ? 'checked' : '' }}>
                <label class="form-check-label">
                    Producto activo
                </label>
            </div>

            <button class="btn btn-success w-100">
                Actualizar producto
            </button>

            <a href="{{ route('productos.index') }}" class="btn btn-link w-100 mt-2">
                Volver
            </a>

        </div>
    </div>

</form>

@endsection