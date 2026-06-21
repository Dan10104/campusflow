<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('codigo')->with('permisos')->get();
        return Inertia::render('Roles/Index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permiso::orderBy('nombre')->get(['codigo','nombre']);
        return Inertia::render('Roles/Form', [
            'mode' => 'create',
            'role' => null,
            'permisos' => $permisos,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'permisos'   => 'required|array',
        ]);
        $role = Role::create($data);
        $role->permisos()->sync($data['permisos'] ?? []);
        return redirect()->route('roles.index')
                         ->with('success', 'Rol creado exitosamente');
    }

    public function edit(Role $role)
    {
        $permisos = Permiso::orderBy('nombre')->get(['codigo','nombre']);
        $role->load('permisos');
        
        return Inertia::render('Roles/Form', [
            'mode' => 'edit',
            'role' => $role,
            'permisos' => $permisos,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'permisos'   => 'required|array',
        ]);
        $role->update($data);
        $role->permisos()->sync($data['permisos'] ?? []);
        return redirect()->route('roles.index')
                         ->with('success', 'Rol actualizado exitosamente');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')
                         ->with('success', 'Rol eliminado');
    }

    // Mostrar página para asignar funcionalidades
    public function editFunctionalities(Role $role)
    {
        $role->load('permisos');

        $permisos = $role->permisos;

        return Inertia::render('Roles/Functionalities', [
            'role' => $role,
            'permisos' => $permisos,
        ]);
    }


    // Guardar asignaciones
    public function updateFunctionalities(Request $request, Role $role)
{
    // 1. Valida entrada
    $data = $request->validate([
        'permiso_id' => 'required|integer|exists:permisos,codigo',
        'permisos'         => 'array'
    ]);

    // 2. Encuentra o crea el Permiso para esa funcionalidad
    $permiso = $role->permisos()
        ->firstWhere('codigo', $data['permiso_id'])
        ?? $role->permisos()->create([
               'codigo' => $data['permiso_id'],
               'estado'           => true,
           ]);

    // 3. Prepara el array para sync(): [ recurso_id => ['activo'=>true], ... ]
    $sync = [];
    foreach ($data['permisos'] ?? [] as $permisoId) {
        $sync[$permisoId] = ['estado' => true];
        }

        // 4. Hace sync (esto crea nuevas relaciones, elimina las quitadas)
        $role->permisos()->sync($sync);

        return redirect()->route('roles.index')
                         ->with('success', 'Permisos actualizados exitosamente');
    }
}
