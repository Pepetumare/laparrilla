@extends('layouts.app')

@section('content')

<h2 class="mb-4">Editar proveedor</h2>

<form action="{{ route('proveedores.update', $proveedor) }}" method="POST">

    @csrf
    @method('PUT')

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">
                    Nombre
                </label>

                <input
                    type="text"
                    name="nombre"
                    class="form-control"
                    value="{{ old('nombre', $proveedor->nombre) }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Teléfono
                </label>

                <input
                    type="text"
                    name="telefono"
                    class="form-control"
                    value="{{ old('telefono', $proveedor->telefono) }}"
                >
            </div>

            <div class="mb-4">

                <label class="form-label">
                    Productos asociados
                </label>

                <div class="row">

                    @foreach($productos as $producto)

                        <div class="col-6 mb-2">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="productos[]"
                                    value="{{ $producto->id }}"
                                    id="producto{{ $producto->id }}"
                                    {{ $proveedor->productos->contains($producto->id) ? 'checked' : '' }}
                                >

                                <label
                                    class="form-check-label"
                                    for="producto{{ $producto->id }}"
                                >
                                    {{ $producto->nombre }}
                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="activo"
                    {{ old('activo', $proveedor->activo) ? 'checked' : '' }}
                >

                <label class="form-check-label">
                    Proveedor activo
                </label>

            </div>

            <button class="btn btn-success w-100">
                Actualizar proveedor
            </button>

            <a href="{{ route('proveedores.index') }}" class="btn btn-link w-100 mt-2">
                Volver
            </a>

        </div>
    </div>

</form>

@endsection