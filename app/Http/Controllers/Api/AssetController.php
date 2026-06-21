<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Listado paginado de activos (equipos, recursos, etc.)
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $tipo = $request->query('tipo'); // ID del tipo de activo

        $query = Activo::with(['tipoActivo']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($tipo) {
            $query->where('tipo_activo_id', $tipo);
        }

        $paginator = $query->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Lista de activos consultada correctamente',
            'data' => $paginator
        ], 200);
    }
}
