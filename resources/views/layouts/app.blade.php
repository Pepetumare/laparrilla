<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carnicería La Parrilla</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --carbon: #080403;
            --madera: #2D130C;
            --madera-rojiza: #672014;
            --rojo-parrilla: #B62128;
            --rojo-oscuro: #8C1A1F;
            --carne: #CC685D;
            --blanco-humo: #EDEDEC;
            --gris-metal: #BDB0A7;
            --gris-acero: #746E6A;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(182, 33, 40, 0.18), transparent 35%),
                linear-gradient(135deg, var(--carbon), var(--madera));
            color: var(--blanco-humo);
            min-height: 100vh;
        }

        .navbar-parrilla {
            background: linear-gradient(90deg, var(--carbon), var(--rojo-oscuro), var(--rojo-parrilla));
            border-bottom: 1px solid rgba(237, 237, 236, 0.12);
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 4px;
        }

        .brand-title {
            color: var(--blanco-humo);
            font-weight: 800;
            letter-spacing: .3px;
        }

        .main-shell {
            max-width: 1320px;
        }

        .card,
        details.card {
            background: rgba(237, 237, 236, 0.96);
            color: #1f1f1f;
            border: 1px solid rgba(189, 176, 167, 0.45);
            border-radius: 18px;
            box-shadow: 0 16px 35px rgba(0, 0, 0, 0.22);
        }

        .card-header {
            border-radius: 18px 18px 0 0 !important;
        }

        .btn-danger,
        .btn-success {
            background: var(--rojo-parrilla);
            border-color: var(--rojo-parrilla);
        }

        .btn-danger:hover,
        .btn-success:hover {
            background: var(--rojo-oscuro);
            border-color: var(--rojo-oscuro);
        }

        .btn-light {
            background: var(--blanco-humo);
            border-color: var(--gris-metal);
            color: var(--carbon);
            font-weight: 600;
        }

        .btn-light:hover {
            background: var(--gris-metal);
            color: var(--carbon);
        }

        .btn-dark {
            background: var(--carbon);
            border-color: var(--carbon);
        }

        .text-danger {
            color: var(--rojo-parrilla) !important;
        }

        .badge.bg-danger {
            background: var(--rojo-parrilla) !important;
        }

        .badge.bg-success {
            background: #1d7f4c !important;
        }

        .btn-accion {
            width: 90px;
            height: 38px;
            padding: 6px 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .page-title {
            color: var(--blanco-humo);
            font-weight: 800;
        }

        .page-subtitle {
            color: var(--gris-metal);
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            border-color: var(--gris-metal);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--rojo-parrilla);
            box-shadow: 0 0 0 .2rem rgba(182, 33, 40, .18);
        }

        .alert-success {
            background: rgba(204, 104, 93, 0.18);
            border-color: rgba(204, 104, 93, 0.45);
            color: var(--blanco-humo);
        }

        .content-card {
            background: rgba(237, 237, 236, 0.96);
            color: #1f1f1f;
            border-radius: 18px;
        }

        @media (max-width: 768px) {
            .navbar-actions {
                width: 100%;
                margin-top: 12px;
            }

            .navbar-actions .btn,
            .navbar-actions form {
                flex: 1;
            }

            .navbar-actions .btn {
                width: 100%;
            }
        }
    </style>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#B62128">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="La Parrilla">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-la-parrilla.png') }}">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-parrilla shadow-sm py-3">
        <div class="container-fluid">

            <a href="{{ route('ingresos.index') }}" class="navbar-brand d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-la-parrilla.png') }}" class="brand-logo" alt="La Parrilla">
                <span class="brand-title">La Parrilla</span>
            </a>

            @auth
                <div class="d-flex gap-2 align-items-center flex-wrap navbar-actions">

                    <a href="{{ route('ingresos.index') }}" class="btn btn-sm btn-light">
                        Ingresos
                    </a>

                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light">
                        Productos
                    </a>

                    <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-light">
                        Proveedores
                    </a>
                    <a href="{{ route('procesamientos.index') }}" class="btn btn-sm btn-light">
                        Procesamientos
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

    <main class="container main-shell py-5">
        @yield('content')
    </main>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function() {
                        console.log('Service Worker registrado correctamente');
                    })
                    .catch(function(error) {
                        console.log('Error registrando Service Worker:', error);
                    });
            });
        }
    </script>
</body>

</html>
