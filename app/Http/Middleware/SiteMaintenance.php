<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks the public site (pages, forms, checkout) while config('maintenance.enabled')
 * is on. Admin, webhooks and .well-known stay reachable. See config/maintenance.php.
 */
class SiteMaintenance
{
    public const PREVIEW_COOKIE = 'site_preview';

    /** True when this browser holds a valid ?preview=<secret> cookie (team member). */
    public static function hasPreviewAccess(Request $request): bool
    {
        $secret = (string) config('maintenance.secret', '');

        return $secret !== ''
            && hash_equals(hash_hmac('sha256', 'site-preview', $secret), (string) $request->cookies->get(self::PREVIEW_COOKIE, ''));
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('maintenance.enabled')) {
            return $next($request);
        }

        if ($request->is(...config('maintenance.except', []))) {
            return $next($request);
        }

        $secret = (string) config('maintenance.secret', '');

        // ?preview=<secret> → set the bypass cookie and reload without the query string
        if ($secret !== '' && hash_equals($secret, (string) $request->query('preview', ''))) {
            return redirect($request->url())
                ->withCookie(cookie(self::PREVIEW_COOKIE, hash_hmac('sha256', 'site-preview', $secret), 60 * 24 * 7));
        }

        if (self::hasPreviewAccess($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The site is under maintenance. Please check back soon.',
            ], 503, ['Retry-After' => 3600]);
        }

        return response()->view('maintenance', [], 503, ['Retry-After' => 3600]);
    }
}
