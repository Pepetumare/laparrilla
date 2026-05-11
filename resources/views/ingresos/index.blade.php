@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Ingresos de Mercadería</h2>
            <small class="text-muted">Resumen agrupado por día</small>
        </div>

        <a href="{{ route('ingresos.create') }}" class="btn btn-danger">
            Nuevo ingreso
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-4">

            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small class="text-muted">Ingresos hoy</small>
                        <h3 class="mb-0">{{ $totalIngresosHoy }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small class="text-muted">Cajas hoy</small>
                        <h3 class="mb-0">{{ $totalCajasHoy }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <small class="text-muted">Kilos hoy</small>
                        <h3 class="mb-0 text-danger">
                            {{ number_format($totalKgHoy, 2) }} kg
                        </h3>
                    </div>
                </div>
            </div>

        </div>

    @forelse($ingresosPorDia as $fecha => $ingresos)
        @php
            $totalCajasDia = $ingresos->sum(fn($ingreso) => $ingreso->detalles->count());
            $totalKgDia = $ingresos->sum(fn($ingreso) => $ingreso->detalles->sum('peso_kg'));
        @endphp
        @php
            $resumen = "📦 *Ingreso de Mercadería*\n";
            $resumen .= '📅 ' . \Carbon\Carbon::parse($fecha)->format('d-m-Y') . "\n\n";

            foreach ($ingresos as $ingreso) {
                $resumen .= '🏢 *Proveedor:* ' . ($ingreso->proveedor->nombre ?? 'Sin proveedor') . "\n";

                $detallesPorProducto = $ingreso->detalles->groupBy('producto_id');

                foreach ($detallesPorProducto as $detallesProducto) {
                    $producto = $detallesProducto->first()->producto;

                    $resumen .= "\n🥩 *" . $producto->nombre . "*\n";

                    foreach ($detallesProducto as $detalle) {
                        $resumen .=
                            '• Caja ' . $detalle->numero_caja . ' → ' . number_format($detalle->peso_kg, 2) . " kg\n";
                    }

                    $resumen .= '➡ Total: ' . number_format($detallesProducto->sum('peso_kg'), 2) . " kg\n";
                }

                $resumen .= "\n";
            }

            $resumen .= '📦 Total cajas: ' . $totalCajasDia . "\n";
            $resumen .= '⚖ Total general: ' . number_format($totalKgDia, 2) . ' kg';
        @endphp

        <details class="card shadow-sm mb-3">

            <summary class="card-header bg-white" style="cursor:pointer; list-style:none;">
                <div class="d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="mb-1">
                            {{ \Carbon\Carbon::parse($fecha)->format('d-m-Y') }}
                        </h5>

                        <small class="text-muted">
                            {{ $ingresos->count() }} ingresos registrados
                        </small>
                    </div>

                    <div class="text-end">
                        <strong>{{ $totalCajasDia }} cajas</strong>
                        <br>
                        <span class="text-danger fw-bold">
                            {{ number_format($totalKgDia, 2) }} kg
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm btn-success mt-2"
                        onclick='copiarResumen(event, @json($resumen))'>
                        Copiar WhatsApp
                    </button>
                </div>
            </summary>

            <div class="card-body">

                @foreach ($ingresos as $ingreso)
                    <div class="border rounded p-3 mb-3 bg-light">

                        <div class="d-flex justify-content-between mb-2">

                            <div>
                                <strong>
                                    {{ $ingreso->proveedor->nombre ?? 'Sin proveedor' }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    Ingreso #{{ $ingreso->id }}
                                </small>
                            </div>

                            <div class="text-end">
                                <strong>
                                    {{ $ingreso->detalles->count() }} cajas
                                </strong>

                                <br>

                                <span class="text-danger fw-bold">
                                    {{ number_format($ingreso->detalles->sum('peso_kg'), 2) }} kg
                                </span>
                            </div>

                        </div>

                        @foreach ($ingreso->detalles as $detalle)
                            <div class="d-flex justify-content-between border-top py-2">

                                <div>
                                    {{ $detalle->producto->nombre }}
                                    <br>
                                    <small class="text-muted">
                                        Caja {{ $detalle->numero_caja }}
                                    </small>
                                </div>

                                <strong>
                                    {{ number_format($detalle->peso_kg, 2) }} kg
                                </strong>

                            </div>
                        @endforeach

                    </div>
                @endforeach

            </div>

        </details>

    @empty

        <div class="card shadow-sm">
            <div class="card-body text-center text-muted">
                No hay ingresos registrados.
            </div>
        </div>
    @endforelse

    <script>
function copiarResumen(event, texto) {
    event.preventDefault();
    event.stopPropagation();

    navigator.clipboard.writeText(texto)
        .then(() => {
            alert('Resumen copiado para WhatsApp');
        })
        .catch(() => {
            alert('No se pudo copiar el resumen');
        });
}
</script>
@endsection
