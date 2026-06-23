<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte del Dashboard de CampusFlow</title>
    <style>
        @page {
            margin: 24px;
        }

        body {
            background: #ffffff;
            color: #0f172a;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        h1, h2, h3, p {
            margin: 0;
        }

        .header {
            border-bottom: 3px solid #2563eb;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .eyebrow {
            color: #2563eb;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 24px;
            margin-top: 4px;
        }

        .muted {
            color: #64748b;
        }

        .kpis {
            margin-bottom: 18px;
            width: 100%;
        }

        .kpi {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            display: inline-block;
            margin-right: 10px;
            padding: 12px;
            vertical-align: top;
            width: 30%;
        }

        .kpi-label {
            color: #475569;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kpi-value {
            color: #0b1f3a;
            font-size: 24px;
            font-weight: bold;
            margin-top: 4px;
        }

        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        h2 {
            border-left: 4px solid #2563eb;
            font-size: 15px;
            margin-bottom: 8px;
            padding-left: 8px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th {
            background: #0b1f3a;
            color: #ffffff;
            font-size: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        th, td {
            border: 1px solid #e2e8f0;
            padding: 7px 8px;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .empty {
            color: #64748b;
            font-style: italic;
            text-align: center;
        }

        .grid {
            width: 100%;
        }

        .col {
            display: inline-block;
            margin-right: 1%;
            vertical-align: top;
            width: 48%;
        }

        .footer {
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 10px;
            margin-top: 18px;
            padding-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <p class="eyebrow">CampusFlow por Nexora Tech</p>
        <h1>Reporte del Dashboard de CampusFlow</h1>
        <p class="muted">
            Generado el {{ $fechaGeneracion->format('d/m/Y H:i') }}
        </p>
    </header>

    <section class="kpis">
        <article class="kpi">
            <p class="kpi-label">Reservas este mes</p>
            <p class="kpi-value">{{ data_get($kpis, 'reservas_mes', 0) }}</p>
        </article>
        <article class="kpi">
            <p class="kpi-label">Activos prestados</p>
            <p class="kpi-value">{{ data_get($kpis, 'activos_prestados', 0) }}</p>
        </article>
        <article class="kpi">
            <p class="kpi-label">Aulas en mantenimiento</p>
            <p class="kpi-value">{{ data_get($kpis, 'aulas_mantenimiento', 0) }}</p>
        </article>
    </section>

    <div class="grid">
        <section class="section col">
            <h2>Reservas por estado</h2>
            <table>
                <thead>
                    <tr>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(data_get($charts, 'estados', []) as $estado)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', data_get($estado, 'estado', 'No disponible'))) }}</td>
                            <td>{{ data_get($estado, 'total', 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty">No existen registros disponibles para este indicador.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="section col">
            <h2>Aulas más solicitadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Aula</th>
                        <th>Total de reservas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(data_get($charts, 'aulas_top', []) as $aula)
                        <tr>
                            <td>{{ data_get($aula, 'nombre', 'No disponible') }}</td>
                            <td>{{ data_get($aula, 'total', 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty">No existen registros disponibles para este indicador.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>

    <section class="section">
        <h2>Próximas reservas</h2>
        <table>
            <thead>
                <tr>
                    <th>Aula</th>
                    <th>Código de aula</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                </tr>
            </thead>
            <tbody>
                @forelse(data_get($listas, 'proximas_reservas', []) as $reserva)
                    <tr>
                        <td>{{ data_get($reserva, 'aula.modulo.nombre', 'No disponible') }}</td>
                        <td>{{ data_get($reserva, 'aula_codigo', 'No disponible') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', data_get($reserva, 'estado', 'No disponible'))) }}</td>
                        <td>
                            @if(data_get($reserva, 'inicio'))
                                {{ \Carbon\Carbon::parse(data_get($reserva, 'inicio'))->format('d/m/Y H:i') }}
                            @else
                                No disponible
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">No existen registros disponibles para este indicador.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Activos en mantenimiento</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody>
                @forelse(data_get($listas, 'activos_mantenimiento', []) as $activo)
                    <tr>
                        <td>{{ data_get($activo, 'codigo', 'No disponible') }}</td>
                        <td>{{ data_get($activo, 'descripcion', 'No disponible') }}</td>
                        <td>{{ data_get($activo, 'tipo_activo.nombre') ?? data_get($activo, 'tipoActivo.nombre') ?? 'No disponible' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty">No existen registros disponibles para este indicador.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="section">
        <h2>Predicción de demanda</h2>
        <table>
            <thead>
                <tr>
                    <th>Aula</th>
                    <th>Nivel de demanda</th>
                    <th>Probabilidad</th>
                    <th>Hora pico</th>
                </tr>
            </thead>
            <tbody>
                @forelse(data_get($listas, 'predicciones', []) as $prediccion)
                    <tr>
                        <td>{{ data_get($prediccion, 'aula', 'No disponible') }}</td>
                        <td>{{ data_get($prediccion, 'nivel_demanda', 'No disponible') }}</td>
                        <td>{{ data_get($prediccion, 'probabilidad', 0) }}%</td>
                        <td>{{ data_get($prediccion, 'hora_pico', 'No disponible') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty">No existen registros disponibles para este indicador.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <footer class="footer">
        Reporte generado por CampusFlow. No incluye contraseñas, tokens, sesiones, hashes ni listados completos de usuarios.
    </footer>
</body>
</html>
