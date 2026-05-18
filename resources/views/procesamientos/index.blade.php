@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 page-title">Procesamientos</h2>
        <small class="page-subtitle">Control de cortes, bolsas útiles y merma automática</small>
    </div>

    <a href="{{ route('procesamientos.create') }}" class="btn btn-danger">
        Nuevo procesamiento
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row mb-4">
    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Peso procesado en tandas</small>
                <h3 class="mb-0">
                    {{ number_format($procesamientos->sum('peso_inicial_kg'), 2) }} kg
                </h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Peso útil</small>
                <h3 class="mb-0">
                    {{ number_format($procesamientos->sum('peso_util_kg'), 2) }} kg
                </h3>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <small class="text-muted">Merma total</small>
                <h3 class="mb-0 text-danger">
                    {{ number_format($procesamientos->sum('merma_kg'), 2) }} kg
                </h3>
            </div>
        </div>
    </div>
</div>

@forelse($procesamientos as $procesamiento)

    @php
        $porcentajeMerma = $procesamiento->peso_inicial_kg > 0
            ? ($procesamiento->merma_kg / $procesamiento->peso_inicial_kg) * 100
            : 0;
    @endphp

    <details class="card shadow-sm mb-3">
        <summary class="card-header bg-white" style="cursor:pointer; list-style:none;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div>
                    <h5 class="mb-1">
                        {{ $procesamiento->producto->nombre ?? 'Producto' }}
                    </h5>

                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($procesamiento->fecha_procesamiento)->format('d-m-Y') }}
                        · {{ $procesamiento->sucursal->nombre ?? 'Sin sucursal' }}
                    </small>
                </div>

                <div class="text-end">
                    <strong>
                        Útil: {{ number_format($procesamiento->peso_util_kg, 2) }} kg
                    </strong>
                    <br>
                    <span class="text-danger fw-bold">
                        Merma: {{ number_format($procesamiento->merma_kg, 2) }} kg
                        ({{ number_format($porcentajeMerma, 1) }}%)
                    </span>
                </div>

            </div>
        </summary>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-12 col-md-4">
                    <strong>Proveedor:</strong>
                    {{ $procesamiento->ingreso->proveedor->nombre ?? 'Sin proveedor' }}
                </div>

                <div class="col-12 col-md-4">
                    <strong>Peso procesado en esta tanda:</strong>
                    {{ number_format($procesamiento->peso_inicial_kg, 2) }} kg
                </div>

                <div class="col-12 col-md-4">
                    <strong>Bolsas:</strong>
                    {{ $procesamiento->detalles->count() }}
                </div>
            </div>

            @foreach($procesamiento->detalles as $detalle)
                <div class="d-flex justify-content-between border-top py-2">
                    <div>
                        Bolsa {{ $detalle->numero_bolsa }}
                        @if($detalle->observacion)
                            <br>
                            <small class="text-muted">
                                {{ $detalle->observacion }}
                            </small>
                        @endif
                    </div>

                    <strong>
                        {{ number_format($detalle->peso_kg, 2) }} kg
                    </strong>
                </div>
            @endforeach

        </div>
    </details>

@empty

    <div class="card shadow-sm">
        <div class="card-body text-center text-muted">
            No hay procesamientos registrados.
        </div>
    </div>

@endforelse

@endsection