@extends('layouts.app')

@section('content')

<h2 class="mb-4">Nuevo ingreso</h2>

<form action="{{ route('ingresos.store') }}" method="POST">

    @csrf

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Proveedor</label>

                <select name="proveedor_id" class="form-select" required>

                    <option value="">Seleccione</option>

                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">
                            {{ $proveedor->nombre }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha</label>

                <input
                    type="date"
                    name="fecha_ingreso"
                    class="form-control"
                    value="{{ date('Y-m-d') }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Producto</label>

                <select name="producto_id" class="form-select" required>

                    <option value="">Seleccione</option>

                    @foreach($productos as $producto)
                        <option value="{{ $producto->id }}">
                            {{ $producto->nombre }}
                        </option>
                    @endforeach

                </select>
            </div>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5>Cajas</h5>

                <button
                    type="button"
                    class="btn btn-sm btn-danger"
                    onclick="agregarCaja()"
                >
                    + Agregar caja
                </button>

            </div>

            <div id="contenedor-cajas"></div>

            <div class="mt-4">

                <h5>
                    Total KG:
                    <span id="total-kg">0.00</span>
                </h5>

            </div>

            <button class="btn btn-success w-100 mt-4">
                Guardar ingreso
            </button>

        </div>
    </div>

</form>

<script>

let numeroCaja = 1;

function agregarCaja() {

    const contenedor = document.getElementById('contenedor-cajas');

    const html = `
        <div class="border rounded p-3 mb-3 caja-item">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Caja ${numeroCaja}</strong>

                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    onclick="eliminarCaja(this)"
                >
                    X
                </button>
            </div>

            <input
                type="hidden"
                name="cajas[${numeroCaja}][numero_caja]"
                value="${numeroCaja}"
            >

            <div>
                <label class="form-label">Peso KG</label>

                <input
                    type="number"
                    step="0.01"
                    name="cajas[${numeroCaja}][peso_kg]"
                    class="form-control peso-input"
                    required
                    oninput="calcularTotal()"
                >
            </div>

        </div>
    `;

    contenedor.insertAdjacentHTML('beforeend', html);

    numeroCaja++;
}

function eliminarCaja(btn) {

    btn.closest('.caja-item').remove();

    calcularTotal();
}

function calcularTotal() {

    let total = 0;

    document.querySelectorAll('.peso-input').forEach(input => {

        total += parseFloat(input.value) || 0;

    });

    document.getElementById('total-kg').innerText = total.toFixed(2);
}

agregarCaja();

</script>

@endsection