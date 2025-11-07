<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureReferralPartner
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isReferralPartner()) {
            return redirect()->route('profile')
                ->with('error', 'هذه الصفحة متاحة فقط لشركاء برنامج الإحالات.');
        }

        return $next($request);
    }
}
