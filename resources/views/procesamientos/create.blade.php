@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 page-title">Nuevo procesamiento</h2>
            <small class="page-subtitle">Registra bolsas útiles y calcula merma automáticamente</small>
        </div>

        <a href="{{ route('procesamientos.index') }}" class="btn btn-light">
            Volver
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('procesamientos.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">Ingreso recibido</label>

                    <select name="ingreso_mercaderia_id" id="ingresoSelect" class="form-select" required
                        onchange="cargarProductosIngreso()">
                        <option value="">Seleccione un ingreso</option>

                        @foreach ($ingresos as $ingreso)
                            @php
                                $totalIngreso = $ingreso->detalles->sum('peso_kg');
                            @endphp

                            <option value="{{ $ingreso->id }}">
                                #{{ $ingreso->id }}
                                · {{ \Carbon\Carbon::parse($ingreso->fecha_ingreso)->format('d-m-Y') }}
                                · {{ $ingreso->proveedor->nombre ?? 'Sin proveedor' }}
                                · {{ number_format($totalIngreso, 2) }} kg
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Producto a procesar</label>

                    <select name="producto_id" id="productoSelect" class="form-select" required
                        onchange="actualizarPesoInicial()">
                        <option value="">Primero seleccione un ingreso</option>
                    </select>
                </div>

                <div class="row mb-3">

                    <div class="col-12 col-md-4 mb-3">
                        <div class="content-card border rounded p-3">
                            <small class="text-muted">Total recibido</small>
                            <h5 class="mb-0">
                                <span id="totalRecibido">0.00</span> kg
                            </h5>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <div class="content-card border rounded p-3">
                            <small class="text-muted">Ya procesado</small>
                            <h5 class="mb-0">
                                <span id="totalProcesado">0.00</span> kg
                            </h5>
                        </div>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <div class="content-card border rounded p-3">
                            <small class="text-muted">Pendiente</small>
                            <h5 class="mb-0 text-danger">
                                <span id="totalPendiente">0.00</span> kg
                            </h5>
                        </div>
                    </div>

                </div>

                <div class="mb-3">
                    <label class="form-label">Peso que procesaré ahora</label>

                    <input type="number" step="0.01" name="peso_inicial_kg" id="pesoInicial" class="form-control"
                        required min="0.01" oninput="validarPesoProcesado(); calcularTotales();"
                        placeholder="Ej: 25.00">

                    <small class="text-muted">
                        No puede ser mayor al peso pendiente.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observación</label>

                    <textarea name="observacion" class="form-control" rows="2"
                        placeholder="Ej: Corte en sierra, pollo escurrido, limpieza, etc."></textarea>
                </div>

            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Bolsas / peso útil</h5>

                    <button type="button" class="btn btn-danger btn-sm" onclick="agregarBolsa()">
                        + Agregar bolsa
                    </button>
                </div>

                <div id="contenedorBolsas"></div>

                <hr>

                <div class="row">
                    <div class="col-12 col-md-4 mb-3">
                        <small class="text-muted">Total útil</small>
                        <h4>
                            <span id="totalUtil">0.00</span> kg
                        </h4>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <small class="text-muted">Merma automática</small>
                        <h4 class="text-danger">
                            <span id="mermaCalculada">0.00</span> kg
                        </h4>
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                        <small class="text-muted">% merma</small>
                        <h4>
                            <span id="porcentajeMerma">0.0</span>%
                        </h4>
                    </div>
                </div>

                <button class="btn btn-success w-100 mt-3">
                    Guardar procesamiento
                </button>

            </div>
        </div>

    </form>

    <script>
        const ingresosData = @json($ingresosData);

        let numeroBolsa = 1;
        let pendienteActual = 0;

        function cargarProductosIngreso() {
            const ingresoId = document.getElementById('ingresoSelect').value;
            const productoSelect = document.getElementById('productoSelect');

            productoSelect.innerHTML = '<option value="">Seleccione producto</option>';

            limpiarResumenProducto();

            if (!ingresoId || !ingresosData[ingresoId]) {
                return;
            }

            if (ingresosData[ingresoId].length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Este ingreso no tiene productos pendientes';
                productoSelect.appendChild(option);
                return;
            }

            ingresosData[ingresoId].forEach(producto => {
                const option = document.createElement('option');

                option.value = producto.producto_id;
                option.dataset.total = producto.peso_total;
                option.dataset.procesado = producto.peso_procesado;
                option.dataset.pendiente = producto.peso_pendiente;

                option.textContent = `${producto.producto_nombre} · Pendiente ${producto.peso_pendiente} kg`;

                productoSelect.appendChild(option);
            });
        }

        function actualizarPesoInicial() {
            const productoSelect = document.getElementById('productoSelect');
            const selected = productoSelect.options[productoSelect.selectedIndex];

            if (!selected || !selected.dataset.pendiente) {
                limpiarResumenProducto();
                return;
            }

            const total = parseFloat(selected.dataset.total) || 0;
            const procesado = parseFloat(selected.dataset.procesado) || 0;
            const pendiente = parseFloat(selected.dataset.pendiente) || 0;

            pendienteActual = pendiente;

            document.getElementById('totalRecibido').innerText = total.toFixed(2);
            document.getElementById('totalProcesado').innerText = procesado.toFixed(2);
            document.getElementById('totalPendiente').innerText = pendiente.toFixed(2);

            const pesoInicial = document.getElementById('pesoInicial');

            pesoInicial.value = '';
            pesoInicial.max = pendiente.toFixed(2);
            pesoInicial.placeholder = `Máximo ${pendiente.toFixed(2)} kg`;

            calcularTotales();
        }

        function limpiarResumenProducto() {
            pendienteActual = 0;

            document.getElementById('totalRecibido').innerText = '0.00';
            document.getElementById('totalProcesado').innerText = '0.00';
            document.getElementById('totalPendiente').innerText = '0.00';

            document.getElementById('pesoInicial').value = '';
            document.getElementById('pesoInicial').removeAttribute('max');

            calcularTotales();
        }

        function validarPesoProcesado() {
            const input = document.getElementById('pesoInicial');
            const valor = parseFloat(input.value) || 0;

            if (pendienteActual > 0 && valor > pendienteActual) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }

        function agregarBolsa() {
            const contenedor = document.getElementById('contenedorBolsas');

            const html = `
            <div class="border rounded p-3 mb-3 bolsa-item content-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Bolsa ${numeroBolsa}</strong>

                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarBolsa(this)">
                        X
                    </button>
                </div>

                <input
                    type="hidden"
                    name="bolsas[${numeroBolsa}][numero_bolsa]"
                    value="${numeroBolsa}"
                >

                <div class="mb-2">
                    <label class="form-label">Peso kg</label>

                    <input
                        type="number"
                        step="0.01"
                        name="bolsas[${numeroBolsa}][peso_kg]"
                        class="form-control peso-bolsa"
                        required
                        oninput="calcularTotales()"
                    >
                </div>

                <div>
                    <label class="form-label">Observación de bolsa</label>

                    <input
                        type="text"
                        name="bolsas[${numeroBolsa}][observacion]"
                        class="form-control"
                        placeholder="Opcional"
                    >
                </div>
            </div>
        `;

            contenedor.insertAdjacentHTML('beforeend', html);
            numeroBolsa++;
        }

        function eliminarBolsa(btn) {
            btn.closest('.bolsa-item').remove();
            calcularTotales();
        }

        function calcularTotales() {
            const pesoInicial = parseFloat(document.getElementById('pesoInicial').value) || 0;

            let totalUtil = 0;

            document.querySelectorAll('.peso-bolsa').forEach(input => {
                totalUtil += parseFloat(input.value) || 0;
            });

            let merma = pesoInicial - totalUtil;

            let porcentaje = pesoInicial > 0 ?
                (merma / pesoInicial) * 100 :
                0;

            document.getElementById('totalUtil').innerText = totalUtil.toFixed(2);
            document.getElementById('mermaCalculada').innerText = merma.toFixed(2);
            document.getElementById('porcentajeMerma').innerText = porcentaje.toFixed(1);

            const mermaElemento = document.getElementById('mermaCalculada');

            if (merma < 0) {
                mermaElemento.classList.add('text-danger');
            } else {
                mermaElemento.classList.remove('text-danger');
            }
        }

        agregarBolsa();
    </script>
@endsection
