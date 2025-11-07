<?php

namespace App\Http\Middleware;

use App\Services\ReferralProgramService;
use Closure;
use Illuminate\Http\Request;

class CaptureReferralFromRequest
{
    public function __construct(
        protected ReferralProgramService $referrals,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $code = trim((string) $request->query('ref', ''));

        if ($code !== '') {
            $current = $request->session()->get(ReferralProgramService::SESSION_KEY);

            if (!is_array($current) || ($current['code'] ?? null) !== $code) {
                $partner = $this->referrals->resolvePartnerByCode($code);

                if ($partner) {
                    $this->referrals->rememberPartner($partner, $request);
                }
            }
        }

        return $next($request);
    }
}
