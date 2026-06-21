<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Usuarios</title>
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
    <h1>Reporte de Usuarios</h1>
    <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>CI</th>
                <th>Email</th>
                <th>Roles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->codigo }}</td>
                <td>{{ $user->nombre }}</td>
                <td>{{ $user->ci }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @foreach($user->roles as $role)
                        {{ $role->nombre }}@if(!$loop->last), @endif
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por el Sistema de Gestión de Recursos (CICIT)
    </div>
</body>
</html>
