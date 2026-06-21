<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top:1em; }
        th, td { border: 1px solid #444; padding: 4px; text-align: left; }
        .no-border { border: none; }
        .right { text-align: right; }
        .header { margin-bottom: 2em; }
    </style>
</head>
<body>
    <h2>Factura #{{ $factura->codigo }}</h2>

    <table class="no-border">
        <tr>
            <td class="no-border"><strong>Generado por:</strong> {{ $factura->usuario->nombre }}</td>
            <td class="no-border right"><strong>Fecha:</strong> {{ $factura->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td class="no-border"><strong>Pagado por:</strong> {{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
            <td class="no-border"></td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Curso</th>
                <th class="right">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($factura->items as $item)
            <tr>
                <td>{{ $item->curso->nombre }}</td>
                <td class="right">{{ number_format($item->monto, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="right">Total:</th>
                <th class="right">{{ number_format($factura->monto, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
