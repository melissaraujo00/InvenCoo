<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Movimientos</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #333; }

        /* Cabecera corporativa */
        .header { width: 100%; border-bottom: 2px solid #004b87; padding-bottom: 15px; margin-bottom: 20px; }
        .header table { width: 100%; border: none; }
        .header td { border: none; padding: 0; }
        .logo { width: 120px; }
        .company-info { text-align: left; padding-left: 20px; }
        .company-name { font-size: 18px; font-weight: bold; color: #004b87; margin: 0 0 5px 0; }
        .report-meta { text-align: right; }
        .report-title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0 0 5px 0; }

        /* Tablas de datos */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px 6px; text-align: left; }
        .data-table th { background-color: #f8f9fa; font-weight: bold; color: #444; text-transform: uppercase; font-size: 10px; }
        .data-table tr:nth-child(even) { background-color: #fcfcfc; }
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
                    <p style="margin: 0;">Departamento de Auditoría Interna</p>
                </td>
                <td width="40%" class="report-meta">
                    <h2 class="report-title">Auditoría de Movimientos</h2>
                    <p style="margin: 0;"><strong>Período:</strong> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                    <p style="margin: 0;"><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">Transacción</th>
                <th width="12%">Fecha</th>
                <th width="15%">Tipo / Origen</th>
                <th width="15%">Destino</th>
                <th width="30%">Descripción</th>
                <th width="20%">Usuario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $mov)
            @php
                // 1. TRADUCCIÓN DEL TIPO DE MOVIMIENTO
                // Intenta traer el nombre de tu tabla 'types'. Si falla (como el type_id 7), usa la letra.
                $tipoFinal = $mov->type ? $mov->type->name : ($mov->input_type === 'E' ? 'Entrada (Ext)' : 'Salida (Int)');

                // 2. MATRIZ DE ORIGEN Y DESTINO
                $origen = '-';
                $destino = '-';

                // Caso A: Es una Transferencia (Tiene origen o destino declarado)
                if ($mov->origin_office_id || $mov->destination_office_id) {
                    $origen = $mov->originatingBranch->name ?? 'Desconocido';
                    $destino = $mov->destinationBranch->name ?? 'Desconocido';
                }
                // Caso B: Es una Compra o Ajuste (Usa la columna office_id general)
                else {
                    if ($mov->input_type === 'E') {
                        // Las entradas (Compras) vienen de afuera hacia la sucursal
                        $origen = 'Proveedor / Externo';
                        $destino = $mov->office->name ?? 'Sucursal Principal';
                    } else {
                        // Las salidas (Ajustes/Mermas) salen de la sucursal y mueren ahí
                        $origen = $mov->office->name ?? 'Sucursal Principal';
                        $destino = 'Ajuste de Inventario';
                    }
                }
            @endphp
            <tr>
                <td>{{ $mov->transaction_id ?? '#' . $mov->id }}</td>

                <td>{{ \Carbon\Carbon::parse($mov->date_movement)->format('d/m/Y H:i') }}</td>

                <td>
                    <strong>{{ $tipoFinal }}</strong><br>
                    <span style="font-size: 9px; color: #555;">De: {{ $origen }}</span>
                </td>

                <td>{{ $destino }}</td>

                <td>{{ $mov->description ?? 'Sin descripción' }}</td>

                <td>{{ $mov->user->name ?? 'Sistema' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
