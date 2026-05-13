@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-0">Proveedores</h2>
            <small class="text-muted">
                Administración de proveedores
            </small>
        </div>

        <a href="{{ route('proveedores.create') }}" class="btn btn-danger">
            Nuevo proveedor
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            @forelse($proveedores as $proveedor)
                <div class="border-bottom py-3">

                    <div class="d-flex justify-content-between">

                        <div>

                            <strong>
                                {{ $proveedor->nombre }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                {{ $proveedor->telefono ?? 'Sin teléfono' }}
                            </small>

                            <br>

                            @if ($proveedor->activo)
                                <span class="badge bg-success">
                                    Activo
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    Inactivo
                                </span>
                            @endif

                            <div class="mt-2">

                                @foreach ($proveedor->productos as $producto)
                                    <span class="badge bg-danger">
                                        {{ $producto->nombre }}
                                    </span>
                                @endforeach

                            </div>

                        </div>

                        <div class="d-flex gap-2 align-items-start">

                            <a href="{{ route('proveedores.edit', $proveedor) }}"
                                class="btn btn-sm btn-outline-primary btn-accion">
                                Editar
                            </a>

                            <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar proveedor?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger btn-accion">
                                    Eliminar
                                </button>
                            </form>

                        </div>

                    </div>

                </div>

            @empty

                <p class="text-muted text-center mb-0">
                    No hay proveedores registrados.
                </p>
            @endforelse

        </div>
    </div>

@endsection
