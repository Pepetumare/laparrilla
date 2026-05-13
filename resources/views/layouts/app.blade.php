<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnicería La Parrilla - Mariquina</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .btn-accion {
            width: 90px;
            height: 38px;
            padding: 6px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        body {
            background: #f5f6f8;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm">
        <div class="container-fluid">

            <a href="{{ route('ingresos.index') }}" class="navbar-brand fw-bold">
                Sistema Carnicería
            </a>

            @auth
                <div class="d-flex gap-2 align-items-center flex-wrap">

                    <a href="{{ route('ingresos.index') }}" class="btn btn-sm btn-light">
                        Ingresos
                    </a>

                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light">
                        Productos
                    </a>

                    <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-light">
                        Proveedores
                    </a>
                    @if (Auth::user()->rol === 'admin')
                        @php
                            $sucursalesNavbar = \App\Models\Sucursal::where('activo', true)->get();
                        @endphp

                        <form method="POST" action="{{ route('sucursal.cambiar') }}" class="m-0">
                            @csrf

                            <select name="sucursal_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Todas las sucursales</option>

                                @foreach ($sucursalesNavbar as $sucursal)
                                    <option value="{{ $sucursal->id }}"
                                        {{ session('sucursal_activa_id') == $sucursal->id ? 'selected' : '' }}>
                                        {{ $sucursal->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif
                    <span class="text-white small ms-2">
                        {{ Auth::user()->name }}

                        @if (Auth::user()->rol === 'admin')
                            <span class="badge bg-dark">Admin</span>
                        @else
                            <span class="badge bg-secondary">Trabajador</span>
                        @endif
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-dark">
                            Salir
                        </button>
                    </form>

                </div>
            @endauth

        </div>
    </nav>

    <main class="container py-4">
        @yield('content')
    </main>

</body>

</html>
