<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Movimientos</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .logo { width: 150px; }
        .title { text-align: right; float: right; font-size: 18px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if($base64Logo)
            <img src="{{ $base64Logo }}" class="logo" alt="Logo">
        @endif
        <div class="title">
            Auditoría de Movimientos<br>
            <small style="font-size: 10px;">Generado: {{ now()->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Sucursal Destino</th>
                <th>Solicitante</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $mov)
            <tr>
                <td>{{ $mov->id }}</td>
                <td>Transferencia</td>
                <td>{{ $mov->destinationBranch->name ?? 'N/A' }}</td>
                <td>{{ $mov->requestingUser->name ?? 'N/A' }}</td>
                <td>{{ $mov->created_at->format('d/m/Y') }}</td>
                <td>{{ $mov->status->label() ?? $mov->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
