@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 page-title">Stock disponible</h2>
            <small class="page-subtitle">
                Stock actual calculado: kilos útiles procesados menos kilos vendidos en cierres diarios
            </small>
        </div>
    </div>

    <div class="row mb-4">

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Productos con movimiento</small>
                    <h3 class="mb-0">
                        {{ $stockPorProducto->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Stock actual</small>
                    <h3 class="mb-0 text-danger">
                        {{ number_format($stockPorProducto->sum('kilos_disponibles'), 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Vendido informado</small>
                    <h3 class="mb-0">
                        {{ number_format($stockPorProducto->sum('total_vendido'), 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <small class="text-muted">Merma acumulada</small>
                    <h3 class="mb-0">
                        {{ number_format($stockPorProducto->sum('total_merma'), 2) }} kg
                    </h3>
                </div>
            </div>
        </div>

    </div>

    @forelse($stockPorProducto as $stock)
        @php
            $producto = $stock['producto'];
            $porcentajeMerma =
                $stock['total_procesado'] > 0 ? ($stock['total_merma'] / $stock['total_procesado']) * 100 : 0;
        @endphp

        <div class="card shadow-sm mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                    <div>
                        <h5 class="mb-1">
                            {{ $producto->nombre ?? 'Producto' }}
                        </h5>

                        <small class="text-muted">
                            Categoría: {{ $producto->categoria ?? 'Sin categoría' }}
                        </small>
                    </div>

                    <small class="text-muted">Stock actual</small>
                    <h3 class="mb-0 text-danger">
                        {{ number_format($stock['kilos_disponibles'], 2) }} kg
                    </h3>

                </div>

                <hr>

                <div class="row text-center">

                    <div class="col-12 col-md-3 mb-3">
                        <small class="text-muted">Procesado bruto</small>
                        <div class="fw-bold">
                            {{ number_format($stock['total_procesado'], 2) }} kg
                        </div>
                    </div>

                    <div class="col-12 col-md-3 mb-3">
                        <small class="text-muted">Peso útil procesado</small>
                        <div class="fw-bold">
                            {{ number_format($stock['total_util_procesado'], 2) }} kg
                        </div>
                    </div>

                    <div class="col-12 col-md-3 mb-3">
                        <small class="text-muted">Vendido informado</small>
                        <div class="fw-bold">
                            {{ number_format($stock['total_vendido'], 2) }} kg
                        </div>
                    </div>

                    <div class="col-12 col-md-3 mb-3">
                        <small class="text-muted">Merma</small>
                        <div class="fw-bold">
                            {{ number_format($stock['total_merma'], 2) }} kg
                        </div>
                    </div>

                </div>

                <small class="text-muted">
                    Basado en {{ $stock['cantidad_procesamientos'] }} procesamiento(s).
                </small>

            </div>
        </div>

    @empty

        <div class="card shadow-sm">
            <div class="card-body text-center text-muted">
                No hay stock disponible todavía. Primero registra procesamientos.
            </div>
        </div>
    @endforelse
@endsection
