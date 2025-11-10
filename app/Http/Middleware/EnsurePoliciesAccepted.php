<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePoliciesAccepted
{
    /**
     * Force Google-authenticated users to accept policies before accessing the app.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            !$user
            || !$user->requiresPolicyConsent()
            || $this->requestAllowed($request)
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'يجب الموافقة على الشروط وسياسة الخصوصية قبل متابعة استخدام المنصة.',
            ], 409);
        }

        return redirect()->route('policy-consent.show');
    }

    private function requestAllowed(Request $request): bool
    {
        $allowedRoutes = [
            'policy-consent.show',
            'policy-consent.store',
            'logout',
            'locale.switch',
        ];

        $route = $request->route();
        $routeName = $route?->getName();

        if ($routeName && $request->routeIs($allowedRoutes)) {
            return true;
        }

        return false;
    }
}

