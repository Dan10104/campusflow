<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Activos Disponibles</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #777; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .disponible { background-color: #d1fae5; color: #065f46; }
        .prestado { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Reporte de Activos</h1>
    <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Marca / Modelo</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Ubicación</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
            <tr>
                <td>{{ $activo->codigo }}</td>
                <td>{{ $activo->descripcion }}</td>
                <td>{{ $activo->marca }} {{ $activo->modelo }}</td>
                <td>{{ $activo->tipoActivo->nombre ?? 'N/A' }}</td>
                <td>
                    <span class="badge {{ $activo->estado }}">
                        {{ ucfirst($activo->estado) }}
                    </span>
                </td>
                <td>{{ $activo->ubicacion_actual }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por el Sistema de Gestión de Recursos (CICIT)
    </div>
</body>
</html>
