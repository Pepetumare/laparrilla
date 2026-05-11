@extends('layouts.app')

@section('content')

<h2 class="mb-4">Nuevo proveedor</h2>

<form action="{{ route('proveedores.store') }}"
      method="POST">

    @csrf

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">
                    Nombre
                </label>

                <input type="text"
                       name="nombre"
                       class="form-control"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Teléfono
                </label>

                <input type="text"
                       name="telefono"
                       class="form-control">
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
                                >

                                <label class="form-check-label"
                                       for="producto{{ $producto->id }}">

                                    {{ $producto->nombre }}

                                </label>

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

            <div class="form-check mb-4">

                <input class="form-check-input"
                       type="checkbox"
                       name="activo"
                       checked>

                <label class="form-check-label">
                    Proveedor activo
                </label>

            </div>

            <button class="btn btn-success w-100">
                Guardar proveedor
            </button>

        </div>
    </div>

</form>

@endsection