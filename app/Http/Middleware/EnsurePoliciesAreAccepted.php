<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePoliciesAreAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if ($this->isAllowedRoute($request)) {
            return $next($request);
        }

        if ($request->session()->get('policies_accepted') === true) {
            return $next($request);
        }

        return redirect()->route('policies.accept');
    }

    private function isAllowedRoute(Request $request): bool
    {
        return $request->routeIs(
            'policies.terms',
            'policies.privacy',
            'policies.accept',
            'policies.accept.store',
            'logout'
        );
    }
}
