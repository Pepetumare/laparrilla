@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Productos</h2>
        <small class="text-muted">Administración de productos</small>
    </div>

    <a href="{{ route('productos.create') }}" class="btn btn-danger">
        Nuevo producto
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">

        @forelse($productos as $producto)

            <div class="d-flex justify-content-between align-items-center border-bottom py-3">

                <div>
                    <strong>{{ $producto->nombre }}</strong>
                    <br>
                    <small class="text-muted">
                        {{ $producto->categoria }} | {{ $producto->unidad_medida }}
                    </small>

                    <br>

                    @if($producto->activo)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </div>

                <div class="d-flex gap-2">

                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-outline-primary btn-accion">
                        Editar
                    </a>

                    <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                          onsubmit="return confirm('¿Seguro que deseas eliminar este producto?')">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-sm btn-outline-danger btn-accion">
                            Eliminar
                        </button>
                    </form>

                </div>

            </div>

        @empty

            <p class="text-muted text-center mb-0">
                No hay productos registrados.
            </p>

        @endforelse

    </div>
</div>

@endsection