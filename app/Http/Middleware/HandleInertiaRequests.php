<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user();

        if ($user) {
            // Carga permisos y funcionalidad en una sola consulta
            $user->loadMissing('roles.permisos');
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'isAdmin' => $user ? $user->esAdmin() : false,
                'permisos' => $user
                    ? $user->roles
                        ->pluck('permisos')     
                        ->flatten(1)            
                        ->unique('codigo')  
                        ->values()
                        ->toArray()
                    : [],
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'warning' => fn () => $request->session()->get('warning'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }

}
