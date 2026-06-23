<?php

namespace App\Http\Controllers;

use App\Models\AceptacionPolitica;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LegalPolicyController extends Controller
{
    public function terms(): Response
    {
        return Inertia::render('Legal/Terms', [
            'version' => AceptacionPolitica::VERSION_TERMINOS,
            'effectiveDate' => '1 de abril de 2026',
        ]);
    }

    public function privacy(): Response
    {
        return Inertia::render('Legal/Privacy', [
            'version' => AceptacionPolitica::VERSION_PRIVACIDAD,
            'effectiveDate' => '1 de abril de 2026',
        ]);
    }

    public function accept(): Response|RedirectResponse
    {
        if (session()->get('policies_accepted') === true) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Legal/AcceptPolicies', [
            'versions' => $this->versions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($request->session()->get('policies_accepted') === true) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'accepted_terms' => ['accepted'],
            'accepted_privacy' => ['accepted'],
        ]);

        $user = $request->user();
        $sessionHash = AceptacionPolitica::hashSessionIdentifier($request->session()->getId());

        AceptacionPolitica::firstOrCreate(
            [
                'usuario_id' => $user->getAuthIdentifier(),
                'identificador_sesion' => $sessionHash,
            ],
            [
                'version_terminos' => AceptacionPolitica::VERSION_TERMINOS,
                'version_privacidad' => AceptacionPolitica::VERSION_PRIVACIDAD,
                'aceptado_en' => now(),
                'direccion_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        $request->session()->put('policies_accepted', true);

        return redirect()->route('dashboard')->with('success', 'Aceptaste los términos y la política de privacidad para esta sesión.');
    }

    public function apiPolicies(Request $request): JsonResponse
    {
        $identifier = $this->apiSessionIdentifier($request);

        return response()->json([
            'success' => true,
            'data' => [
                'versions' => $this->versions(),
                'requires_policy_acceptance' => ! $this->hasApiAcceptance($request, $identifier),
                'links' => [
                    'terms' => route('policies.terms'),
                    'privacy' => route('policies.privacy'),
                ],
            ],
        ]);
    }

    public function apiAccept(Request $request): JsonResponse
    {
        $request->validate([
            'accepted_terms' => ['accepted'],
            'accepted_privacy' => ['accepted'],
        ]);

        $identifier = $this->apiSessionIdentifier($request);
        $sessionHash = AceptacionPolitica::hashSessionIdentifier($identifier);

        AceptacionPolitica::firstOrCreate(
            [
                'usuario_id' => $request->user()->getAuthIdentifier(),
                'identificador_sesion' => $sessionHash,
            ],
            [
                'version_terminos' => AceptacionPolitica::VERSION_TERMINOS,
                'version_privacidad' => AceptacionPolitica::VERSION_PRIVACIDAD,
                'aceptado_en' => now(),
                'direccion_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Politicas aceptadas correctamente.',
            'data' => [
                'requires_policy_acceptance' => false,
                'versions' => $this->versions(),
            ],
        ]);
    }

    private function versions(): array
    {
        return [
            'terms' => AceptacionPolitica::VERSION_TERMINOS,
            'privacy' => AceptacionPolitica::VERSION_PRIVACIDAD,
        ];
    }

    private function apiSessionIdentifier(Request $request): string
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->getKey()) {
            return 'sanctum-token:'.$token->getKey();
        }

        return 'api-user:'.$request->user()?->getAuthIdentifier().':'.$request->ip().':'.substr((string) $request->userAgent(), 0, 120);
    }

    private function hasApiAcceptance(Request $request, string $identifier): bool
    {
        return AceptacionPolitica::where('usuario_id', $request->user()->getAuthIdentifier())
            ->where('identificador_sesion', AceptacionPolitica::hashSessionIdentifier($identifier))
            ->exists();
    }
}
