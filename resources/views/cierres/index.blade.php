@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 page-title">Cierres diarios</h2>
        <small class="page-subtitle">
            Control de kilos restantes y venta estimada
        </small>
    </div>

    <a href="{{ route('cierres.create') }}" class="btn btn-danger">
        Nuevo cierre
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="row mb-4">

    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Cierres registrados</small>
                <h3 class="mb-0">
                    {{ $cierres->count() }}
                </h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Kilos vendidos informados</small>
                <h3 class="mb-0 text-danger">
                    {{ number_format($cierres->sum(fn($cierre) => $cierre->detalles->sum('kilos_vendidos_kg')), 2) }} kg
                </h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Stock restante calculado</small>
                <h3 class="mb-0">
                    {{ number_format($cierres->sum(fn($cierre) => $cierre->detalles->sum('stock_restante_calculado_kg')), 2) }} kg
                </h3>
            </div>
        </div>
    </div>

</div>

@forelse($cierres as $cierre)

    <details class="card shadow-sm mb-3">
        <summary class="card-header bg-white" style="cursor:pointer; list-style:none;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">
                        {{ \Carbon\Carbon::parse($cierre->fecha_cierre)->format('d-m-Y') }}
                    </h5>

                    <small class="text-muted">
                        {{ $cierre->sucursal->nombre ?? 'Sin sucursal' }}
                        · registrado por {{ $cierre->usuario->name ?? 'Usuario' }}
                    </small>
                </div>

                <div class="text-end">
                    <strong>
                        Vendido informado::
                        {{ number_format($cierre->detalles->sum('kilos_vendidos_kg'), 2) }} kg
                    </strong>
                    <br>
                    <span class="text-danger fw-bold">
                        Stock restante:
                        {{ number_format($cierre->detalles->sum('stock_restante_calculado_kg'), 2) }} kg
                    </span>
                </div>

            </div>
        </summary>

        <div class="card-body">

            @if($cierre->observacion)
                <div class="alert alert-light border">
                    {{ $cierre->observacion }}
                </div>
            @endif

            @foreach($cierre->detalles as $detalle)

                <div class="border rounded p-3 mb-3 bg-light">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <div>
                            <strong>
                                {{ $detalle->producto->nombre ?? 'Producto' }}
                            </strong>

                            <br>

                            <small class="text-muted">
                                Stock inicial:
                                {{ number_format($detalle->stock_disponible_kg, 2) }} kg
                            </small>
                        </div>

                        <div class="text-end">
                            <strong>
                                Vendido:
                                {{ number_format($detalle->kilos_vendidos_kg, 2) }} kg
                            </strong>

                            <br>

                            <span class="text-danger fw-bold">
                                Quedó:
                                {{ number_format($detalle->stock_restante_calculado_kg, 2) }} kg
                            </span>
                        </div>

                    </div>

                    @if($detalle->observacion)
                        <small class="text-muted d-block mt-2">
                            {{ $detalle->observacion }}
                        </small>
                    @endif

                </div>

            @endforeach

        </div>
    </details>

@empty

    <div class="card shadow-sm">
        <div class="card-body text-center text-muted">
            No hay cierres diarios registrados.
        </div>
    </div>

@endforelse

@endsection