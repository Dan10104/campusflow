<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Aulas Disponibles</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <h1>Reporte de Aulas</h1>
    <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Módulo</th>
                <th>Facultad</th>
                <th>Tipo</th>
                <th>Capacidad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($aulas as $aula)
            <tr>
                <td>{{ $aula->codigo }}</td>
                <td>{{ $aula->modulo->nombre ?? 'N/A' }}</td>
                <td>{{ $aula->modulo->facultad->nombre ?? 'N/A' }}</td>
                <td>{{ ucfirst($aula->tipo) }}</td>
                <td>{{ $aula->capacidad }}</td>
                <td>{{ ucfirst($aula->estado) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por el Sistema de Gestión de Recursos (CICIT)
    </div>
</body>
</html>
