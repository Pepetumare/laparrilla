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
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-danger shadow">
        <div class="container-fluid">
            <a href="{{ route('ingresos.index') }}" class="navbar-brand mb-0 h1">
                La Parrilla - San José de la Mariquina
            </a>

            <div class="d-flex gap-2">
                <a href="{{ route('ingresos.index') }}" class="btn btn-sm btn-light">
                    Ingresos
                </a>

                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-light">
                    Productos
                </a>

                <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-light">
                    Proveedores
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @yield('content')
    </div>

    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>
