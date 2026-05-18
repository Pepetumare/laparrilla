@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 page-title">Reportes</h2>
            <small class="page-subtitle">
                Resumen por fecha, sucursal, producto, merma, ventas y stock
            </small>
        </div>
    </div>

    @php
        $resumenWhatsapp = "📊 *Reporte La Parrilla*\n";
        $resumenWhatsapp .= '📅 Desde: ' . \Carbon\Carbon::parse($fechaDesde)->format('d-m-Y') . "\n";
        $resumenWhatsapp .= '📅 Hasta: ' . \Carbon\Carbon::parse($fechaHasta)->format('d-m-Y') . "\n\n";

        if (Auth::user()->rol === 'admin' && $sucursalId) {
            $sucursalSeleccionada = $sucursales->firstWhere('id', $sucursalId);
            $resumenWhatsapp .= '🏪 Sucursal: ' . ($sucursalSeleccionada->nombre ?? 'Sucursal seleccionada') . "\n\n";
        } elseif (Auth::user()->rol !== 'admin') {
            $resumenWhatsapp .= '🏪 Sucursal: ' . (Auth::user()->sucursal->nombre ?? 'Sucursal') . "\n\n";
        } else {
            $resumenWhatsapp .= "🏪 Sucursal: Todas\n\n";
        }

        $resumenWhatsapp .= "📦 *Resumen general*\n";
        $resumenWhatsapp .= '• Kilos ingresados: ' . number_format($totalIngresado, 2) . " kg\n";
        $resumenWhatsapp .= '• Peso útil: ' . number_format($totalUtil, 2) . " kg\n";
        $resumenWhatsapp .= '• Merma: ' . number_format($totalMerma, 2) . " kg\n";
        $resumenWhatsapp .= '• % merma: ' . number_format($porcentajeMerma, 1) . "%\n";
        $resumenWhatsapp .= '• Vendido informado: ' . number_format($totalVendido, 2) . " kg\n";
        $resumenWhatsapp .= '• Stock restante: ' . number_format($totalStockRestante, 2) . " kg\n\n";

        $resumenWhatsapp .= "🥩 *Detalle por producto*\n";

        foreach ($productosReporte as $item) {
            $resumenWhatsapp .= "\n*{$item['producto']->nombre}*\n";
            $resumenWhatsapp .= '• Procesado: ' . number_format($item['procesado'], 2) . " kg\n";
            $resumenWhatsapp .= '• Útil: ' . number_format($item['util'], 2) . " kg\n";
            $resumenWhatsapp .= '• Merma: ' . number_format($item['merma'], 2) . " kg\n";
            $resumenWhatsapp .= '• % merma: ' . number_format($item['porcentaje_merma'], 1) . "%\n";
            $resumenWhatsapp .= '• Vendido: ' . number_format($item['vendido'], 2) . " kg\n";
            $resumenWhatsapp .= '• Stock: ' . number_format($item['stock_actual'], 2) . " kg\n";
        }
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('reportes.index') }}" class="row">

                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
                </div>

                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
                </div>

                @if (Auth::user()->rol === 'admin')
                    <div class="col-12 col-md-3 mb-3">
                        <label class="form-label">Sucursal</label>

                        <select name="sucursal_id" class="form-select">
                            <option value="">Todas</option>

                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ $sucursalId == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-12 col-md-3 mb-3">
                    <label class="form-label">Producto</label>

                    <select name="producto_id" class="form-select">
                        <option value="">Todos</option>

                        @foreach ($productos as $producto)
                            <option value="{{ $producto->id }}" {{ $productoId == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 flex-wrap">
                    <button class="btn btn-danger">
                        Filtrar reporte
                    </button>

                    <a href="{{ route('reportes.index') }}" class="btn btn-light">
                        Limpiar
                    </a>

                    <a href="{{ route('reportes.exportarCsv', request()->query()) }}" class="btn btn-success">
                        Exportar CSV
                    </a>

                    <button type="button" class="btn btn-dark"
                        onclick='copiarResumenWhatsapp(@json($resumenWhatsapp))'>
                        Copiar WhatsApp
                    </button>
                </div>

            </form>

        </div>
    </div>

    <div class="row mb-4">

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Kilos ingresados</small>
                    <h3 class="mb-0">
                        {{ number_format($totalIngresado, 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Peso útil</small>
                    <h3 class="mb-0 text-danger">
                        {{ number_format($totalUtil, 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Merma</small>
                    <h3 class="mb-0">
                        {{ number_format($totalMerma, 2) }} kg
                    </h3>

                    <small class="text-muted">
                        {{ number_format($porcentajeMerma, 1) }}%
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Vendido informado</small>
                    <h3 class="mb-0">
                        {{ number_format($totalVendido, 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-0">Reporte por producto</h5>
                    <small class="text-muted">
                        Procesado, útil, merma, vendido y stock actual
                    </small>
                </div>
            </div>

            @forelse($productosReporte as $item)
                <div class="border rounded p-3 mb-3 bg-light">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                        <div>
                            <h5 class="mb-1">
                                {{ $item['producto']->nombre ?? 'Producto' }}
                            </h5>

                            <small class="text-muted">
                                {{ $item['producto']->categoria ?? 'Sin categoría' }}
                            </small>
                        </div>

                        <div class="text-end">
                            <small class="text-muted">Stock actual</small>
                            <h4 class="mb-0 text-danger">
                                {{ number_format($item['stock_actual'], 2) }} kg
                            </h4>
                        </div>

                    </div>

                    <div class="row text-center">

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">Procesado</small>
                            <div class="fw-bold">
                                {{ number_format($item['procesado'], 2) }} kg
                            </div>
                        </div>

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">Útil</small>
                            <div class="fw-bold">
                                {{ number_format($item['util'], 2) }} kg
                            </div>
                        </div>

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">Merma</small>
                            <div class="fw-bold">
                                {{ number_format($item['merma'], 2) }} kg
                            </div>
                        </div>

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">% Merma</small>
                            <div class="fw-bold">
                                {{ number_format($item['porcentaje_merma'], 1) }}%
                            </div>
                        </div>

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">Vendido</small>
                            <div class="fw-bold">
                                {{ number_format($item['vendido'], 2) }} kg
                            </div>
                        </div>

                        <div class="col-6 col-md-2 mb-3">
                            <small class="text-muted">Stock</small>
                            <div class="fw-bold text-danger">
                                {{ number_format($item['stock_actual'], 2) }} kg
                            </div>
                        </div>

                    </div>

                </div>

            @empty

                <div class="text-center text-muted py-4">
                    No hay datos de productos para este rango.
                </div>
            @endforelse

        </div>
    </div>

    <div class="row">

        <div class="col-12 col-lg-6 mb-4">

            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h5 class="mb-3">Procesamientos del período</h5>

                    @forelse($procesamientos as $procesamiento)
                        <div class="border-bottom py-2">

                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>
                                        {{ $procesamiento->producto->nombre ?? 'Producto' }}
                                    </strong>

                                    <br>

                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($procesamiento->fecha_procesamiento)->format('d-m-Y') }}
                                        · {{ $procesamiento->sucursal->nombre ?? 'Sucursal' }}
                                    </small>
                                </div>

                                <div class="text-end">
                                    <strong>
                                        Útil:
                                        {{ number_format($procesamiento->peso_util_kg, 2) }} kg
                                    </strong>

                                    <br>

                                    <span class="text-danger">
                                        Merma:
                                        {{ number_format($procesamiento->merma_kg, 2) }} kg
                                    </span>
                                </div>
                            </div>

                        </div>

                    @empty

                        <p class="text-muted mb-0">
                            No hay procesamientos en este período.
                        </p>
                    @endforelse

                </div>
            </div>

        </div>

        <div class="col-12 col-lg-6 mb-4">

            <div class="card shadow-sm h-100">
                <div class="card-body">

                    <h5 class="mb-3">Cierres del período</h5>

                    @forelse($cierres as $cierre)
                        <details class="border rounded p-3 mb-3 bg-light">
                            <summary style="cursor:pointer;">
                                <strong>
                                    {{ \Carbon\Carbon::parse($cierre->fecha_cierre)->format('d-m-Y') }}
                                </strong>

                                <small class="text-muted">
                                    · {{ $cierre->sucursal->nombre ?? 'Sucursal' }}
                                </small>
                            </summary>

                            <div class="mt-3">

                                @foreach ($cierre->detalles as $detalle)
                                    @if (!$productoId || $detalle->producto_id == $productoId)
                                        <div class="d-flex justify-content-between border-top py-2">

                                            <div>
                                                {{ $detalle->producto->nombre ?? 'Producto' }}
                                            </div>

                                            <div class="text-end">
                                                <strong>
                                                    Vendido:
                                                    {{ number_format($detalle->kilos_vendidos_kg, 2) }} kg
                                                </strong>

                                                <br>

                                                <span class="text-danger">
                                                    Stock restante:
                                                    {{ number_format($detalle->stock_restante_calculado_kg, 2) }} kg
                                                </span>
                                            </div>

                                        </div>
                                    @endif
                                @endforeach

                            </div>
                        </details>

                    @empty

                        <p class="text-muted mb-0">
                            No hay cierres en este período.
                        </p>
                    @endforelse

                </div>
            </div>

        </div>

    </div>


    <script>
        function copiarResumenWhatsapp(texto) {
            navigator.clipboard.writeText(texto)
                .then(() => {
                    alert('Resumen copiado para WhatsApp');
                })
                .catch(() => {
                    alert('No se pudo copiar el resumen. Intente nuevamente.');
                });
        }
    </script>
@endsection
