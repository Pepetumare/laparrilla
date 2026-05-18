@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 page-title">Nuevo cierre diario</h2>
            <small class="page-subtitle">
                Registra los kilos que quedaron al final del día
            </small>
        </div>

        <a href="{{ route('cierres.index') }}" class="btn btn-light">
            Volver
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="row">

                <div class="col-12 col-md-6 mb-3">
                    <small class="text-muted">Sucursal</small>
                    <h5 class="mb-0">
                        {{ $sucursal->nombre ?? 'Sin sucursal' }}
                    </h5>
                </div>

                <div class="col-12 col-md-6 mb-3">
                    <small class="text-muted">Fecha de cierre</small>
                    <h5 class="mb-0">
                        {{ \Carbon\Carbon::parse($fecha)->format('d-m-Y') }}
                    </h5>
                </div>

            </div>

        </div>
    </div>

    <form action="{{ route('cierres.store') }}" method="POST">
        @csrf

        <input type="hidden" name="fecha_cierre" value="{{ $fecha }}">

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Observación general</label>

                    <textarea name="observacion" class="form-control" rows="2" placeholder="Opcional">{{ old('observacion') }}</textarea>
                </div>

            </div>
        </div>

        @forelse($stockPorProducto as $index => $stock)
            @php
                $producto = $stock['producto'];
                $stockDisponible = $stock['stock_disponible_kg'];
            @endphp

            <div class="card shadow-sm mb-3">
                <div class="card-body">

                    <input type="hidden" name="productos[{{ $index }}][producto_id]" value="{{ $producto->id }}">

                    <input type="hidden" name="productos[{{ $index }}][stock_disponible_kg]"
                        value="{{ $stockDisponible }}">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                        <div>
                            <h5 class="mb-1">
                                {{ $producto->nombre }}
                            </h5>

                            <small class="text-muted">
                                Stock disponible:
                                {{ number_format($stockDisponible, 2) }} kg
                            </small>
                        </div>

                        <div class="text-end">
                            <small class="text-muted">Stock restante calculado</small>
                            <h4 class="mb-0 text-danger">
                                <span class="stock-restante" data-stock="{{ $stockDisponible }}">
                                    {{ number_format($stockDisponible, 2) }}
                                </span> kg
                            </h4>
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Kilos vendidos hoy
                        </label>

                        <input type="number" step="0.01" min="0" max="{{ $stockDisponible }}"
                            name="productos[{{ $index }}][kilos_vendidos_kg]" class="form-control kilos-vendidos"
                            data-stock="{{ $stockDisponible }}" required oninput="calcularStockRestante(this)"
                            placeholder="Ej: 19.00">

                        <small class="text-muted">
                            No puede ser mayor a {{ number_format($stockDisponible, 2) }} kg disponibles.
                        </small>
                    </div>

                    <div>
                        <label class="form-label">Observación del producto</label>

                        <input type="text" name="productos[{{ $index }}][observacion]" class="form-control"
                            placeholder="Opcional">
                    </div>

                </div>
            </div>

        @empty

            <div class="card shadow-sm">
                <div class="card-body text-center text-muted">
                    No hay stock disponible para cerrar.
                </div>
            </div>
        @endforelse

        @if ($stockPorProducto->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <small class="text-muted">Total vendido informado</small>
                            <h3 class="mb-0 text-danger">
                                <span id="totalVendido">0.00</span> kg
                            </h3>
                        </div>

                        <div class="text-end">
                            <small class="text-muted">Stock restante calculado</small>
                            <h3 class="mb-0">
                                <span id="totalRestante">0.00</span> kg
                            </h3>
                        </div>
                    </div>

                    <button class="btn btn-success w-100">
                        Guardar cierre diario
                    </button>

                </div>
            </div>
        @endif

    </form>

    <script>
        function calcularStockRestante(input) {
            const stock = parseFloat(input.dataset.stock) || 0;
            const vendido = parseFloat(input.value) || 0;

            const card = input.closest('.card');
            const restanteSpan = card.querySelector('.stock-restante');

            let restante = stock - vendido;

            if (restante < 0) {
                restante = 0;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }

            restanteSpan.innerText = restante.toFixed(2);

            calcularTotales();
        }

        function calcularTotales() {
            let totalVendido = 0;
            let totalRestante = 0;

            document.querySelectorAll('.kilos-vendidos').forEach(input => {
                const stock = parseFloat(input.dataset.stock) || 0;
                const vendido = parseFloat(input.value) || 0;

                totalVendido += vendido;

                let restante = stock - vendido;

                if (restante > 0) {
                    totalRestante += restante;
                }
            });

            document.getElementById('totalVendido').innerText = totalVendido.toFixed(2);
            document.getElementById('totalRestante').innerText = totalRestante.toFixed(2);
        }
    </script>
@endsection
