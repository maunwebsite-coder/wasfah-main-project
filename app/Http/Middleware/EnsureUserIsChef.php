<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsChef
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isChef() || $user->isAdmin()) {
            return $next($request);
        }

        return redirect()
            ->route('onboarding.show')
            ->with('error', 'هذه الصفحة مخصصة للشيفات المعتمدين. يرجى إكمال بياناتك للمتابعة.');
    }
}
