<!DOCTYPE html><html lang="es"><head>
<meta charset="utf-8"><title>Formulario Inscripción</title>
<style>
 body{font-family:sans-serif;font-size:12px}
 table{width:100%;border-collapse:collapse;margin-top:20px}
 th,td{border:1px solid #666;padding:4px}
</style></head><body>

<h2>Formulario de Inscripción / Pago</h2>

<table>
  <tr><th>ID Inscripción</th><td>{{ $i->codigo }}</td></tr>
  <tr><th>Estudiante</th>
      <td>{{ $i->estudiante->nombre }} {{ $i->estudiante->apellido }}</td></tr>
  <tr><th>Curso</th><td>{{ $i->curso->nombre }}</td></tr>
  <tr><th>Monto</th><td>{{ $i->monto }}</td></tr>
  <tr><th>Estado</th><td>{{ $i->estado->nombre }}</td></tr>
  <tr><th>Fecha</th><td>{{ $i->created_at->format('d-m-Y') }}</td></tr>
</table>

</body></html>
