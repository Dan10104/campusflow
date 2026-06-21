<!DOCTYPE html>
<html>
<head>
    <title>Calendario de Aula {{ $aula->codigo }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
        h2 { text-align: center; color: #555; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 12px; color: #777; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .confirmada { background-color: #d1fae5; color: #065f46; }
        .pendiente { background-color: #fef3c7; color: #92400e; }
        .en_uso { background-color: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <h1>Calendario de Aula</h1>
    <h2>{{ $aula->modulo->nombre }} - Aula {{ $aula->codigo }}</h2>
    <p><strong>Facultad:</strong> {{ $aula->modulo->facultad->nombre }}</p>
    <p><strong>Fecha de emisión:</strong> {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Usuario</th>
                <th>Motivo</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservas as $reserva)
            <tr>
                <td>{{ \Carbon\Carbon::parse($reserva->inicio)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva->inicio)->format('H:i') }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva->fin)->format('H:i') }}</td>
                <td>{{ $reserva->usuario->nombre ?? 'N/A' }}</td>
                <td>{{ $reserva->motivo }}</td>
                <td>
                    <span class="badge {{ $reserva->estado }}">
                        {{ ucfirst($reserva->estado) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">No hay reservas programadas para los próximos 30 días.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado por el Sistema de Gestión de Recursos (CICIT)
    </div>
</body>
</html>
