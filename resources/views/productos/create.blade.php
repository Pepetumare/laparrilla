@extends('layouts.app')

@section('content')

<h2 class="mb-4">Nuevo producto</h2>

<form action="{{ route('productos.store') }}" method="POST">
    @csrf

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Nombre del producto</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoría</label>
                <select name="categoria" class="form-select" required>
                    <option value="">Seleccione</option>
                    <option value="Cerdo">Cerdo</option>
                    <option value="Vacuno">Vacuno</option>
                    <option value="Pollo">Pollo</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Unidad de medida</label>
                <select name="unidad_medida" class="form-select" required>
                    <option value="kg">Kg</option>
                    <option value="unidad">Unidad</option>
                    <option value="caja">Caja</option>
                </select>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="activo" checked>
                <label class="form-check-label">
                    Producto activo
                </label>
            </div>

            <button class="btn btn-success w-100">
                Guardar producto
            </button>

            <a href="{{ route('productos.index') }}" class="btn btn-link w-100 mt-2">
                Volver
            </a>

        </div>
    </div>

</form>

@endsection