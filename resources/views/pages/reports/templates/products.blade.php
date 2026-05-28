<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #004b87; padding-bottom: 15px; margin-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .header td { border: none; padding: 0; }
        .logo { width: 120px; }
        .company-info { text-align: left; padding-left: 20px; }
        .company-name { font-size: 18px; font-weight: bold; color: #004b87; margin: 0 0 5px 0; }
        .report-meta { text-align: right; }
        .report-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0 0 5px 0; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px 6px; text-align: left; }
        .data-table th { background-color: #f8f9fa; font-weight: bold; color: #444; text-transform: uppercase; font-size: 10px; }
        .data-table tr:nth-child(even) { background-color: #fcfcfc; }

        .status-critical { color: #d9534f; font-weight: bold; }
        .status-normal { color: #5cb85c; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td width="15%">
                    @if($base64Logo)
                        <img src="{{ $base64Logo }}" class="logo" alt="Logo">
                    @endif
                </td>
                <td width="45%" class="company-info">
                    <h1 class="company-name">Cooperativa de Cafetaleros</h1>
                    <p style="margin: 0;">Ciudad Barrios, San Miguel</p>
                    <p style="margin: 0;">Control de Inventarios</p>
                </td>
                <td width="40%" class="report-meta">
                    <h2 class="report-title">Estado de Stock Global</h2>
                    <p style="margin: 0;"><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="45%">Producto</th>
                <th width="15%">Stock Actual</th>
                <th width="15%">Stock Mínimo</th>
                <th width="15%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>#{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->stock_minimun }}</td>
                @if($product->stock <= $product->stock_minimun)
                    <td class="status-critical">CRÍTICO</td>
                @else
                    <td class="status-normal">Normal</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
