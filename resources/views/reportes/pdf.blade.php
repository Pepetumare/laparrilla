<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte La Parrilla</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #080403;
        }

        .header {
            border-bottom: 3px solid #B62128;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        h1 {
            margin: 0;
            color: #B62128;
            font-size: 24px;
        }

        .subtitle {
            color: #746E6A;
            margin-top: 4px;
        }

        .summary {
            width: 100%;
            margin-bottom: 18px;
            border-collapse: collapse;
        }

        .summary td {
            width: 20%;
            padding: 10px;
            background: #EDEDEC;
            border: 1px solid #BDB0A7;
            text-align: center;
        }

        .summary small {
            display: block;
            color: #746E6A;
            margin-bottom: 4px;
        }

        .summary strong {
            color: #B62128;
            font-size: 14px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #2D130C;
            color: #EDEDEC;
            padding: 8px;
            border: 1px solid #2D130C;
            font-size: 11px;
        }

        table.data td {
            padding: 7px;
            border: 1px solid #BDB0A7;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #746E6A;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <table style="width:100%;">
            <tr>
                <td style="width:90px;">
                    <img src="{{ public_path('images/logo-la-parrilla.png') }}" style="width:75px; height:auto;"
                        alt="La Parrilla">
                </td>

                <td>
                    <h1>Reporte La Parrilla</h1>

                    <div class="subtitle">
                        Desde {{ \Carbon\Carbon::parse($fechaDesde)->format('d-m-Y') }}
                        hasta {{ \Carbon\Carbon::parse($fechaHasta)->format('d-m-Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="summary">
        <tr>
            <td>
                <small>Procesado</small>
                <strong>{{ number_format($totalProcesado, 2) }} kg</strong>
            </td>
            <td>
                <small>Peso util</small>
                <strong>{{ number_format($totalUtil, 2) }} kg</strong>
            </td>
            <td>
                <small>Merma</small>
                <strong>{{ number_format($totalMerma, 2) }} kg</strong>
            </td>
            <td>
                <small>Vendido</small>
                <strong>{{ number_format($totalVendido, 2) }} kg</strong>
            </td>
            <td>
                <small>Stock</small>
                <strong>{{ number_format($totalStock, 2) }} kg</strong>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Sucursal</th>
                <th>Producto</th>
                <th>Categoria</th>
                <th>Procesado</th>
                <th>Util</th>
                <th>Merma</th>
                <th>% Merma</th>
                <th>Vendido</th>
                <th>Stock</th>
            </tr>
        </thead>

        <tbody>
            @forelse($productosReporte as $item)
                <tr>
                    <td>{{ $item['sucursal'] }}</td>
                    <td>{{ $item['producto'] }}</td>
                    <td>{{ $item['categoria'] }}</td>
                    <td class="text-right">{{ number_format($item['procesado'], 2) }} kg</td>
                    <td class="text-right">{{ number_format($item['util'], 2) }} kg</td>
                    <td class="text-right">{{ number_format($item['merma'], 2) }} kg</td>
                    <td class="text-right">{{ number_format($item['porcentaje_merma'], 1) }}%</td>
                    <td class="text-right">{{ number_format($item['vendido'], 2) }} kg</td>
                    <td class="text-right">{{ number_format($item['stock_actual'], 2) }} kg</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">
                        No hay datos para este reporte.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado automaticamente por Sistema La Parrilla
    </div>

</body>

</html>
