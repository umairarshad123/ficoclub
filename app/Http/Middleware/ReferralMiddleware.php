<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ReferralMiddleware
{
    private const VALID_CODES = [
        'DL',
        'EL',
        'NL',
        'EXP',
        'LAL',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ref = strtoupper(trim($request->query('ref', '')));

        if ($ref && in_array($ref, self::VALID_CODES, true)) {
            session(['referral_code' => $ref]);

            Log::info('Referral code captured', [
                'code' => $ref,
                'ip'   => $request->ip(),
                'url'  => $request->fullUrl(),
            ]);

            $response = $next($request);
            $response->withCookie(cookie('referral_code', $ref, 60 * 24 * 30));
            return $response;
        }

        if (!session()->has('referral_code')) {
            $cookieRef = strtoupper(trim($request->cookie('referral_code', '')));
            if ($cookieRef && in_array($cookieRef, self::VALID_CODES, true)) {
                session(['referral_code' => $cookieRef]);
                Log::info('Referral code restored from cookie', ['code' => $cookieRef]);
            }
        }

        return $next($request);
    }
}