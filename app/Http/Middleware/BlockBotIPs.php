<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlockBotIPs
{
    private const BLOCKED_SUBNETS = [
        '138.59.',
        '168.81.',
        '181.177.',
        '152.232.',
        '191.102.',
        '188.137.',
        '94.131.',
        '131.108.',
        '186.65.',
        '168.196.',
    ];

    private const BLOCKED_IPS = [
        '198.46.154.21',
        // Add any IPs outside the above subnets here
    ];

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // 1. Block entire subnets
        foreach (self::BLOCKED_SUBNETS as $subnet) {
            if (str_starts_with($ip, $subnet)) {
                abort(403);
            }
        }

        // 2. Block individual IPs
        if (in_array($ip, self::BLOCKED_IPS)) {
            abort(403);
        }

        // 3. Check dynamically cache-banned IPs
        if (Cache::get('blocked_ip:' . $ip)) {
            abort(403);
        }

        // 4. Check dynamically cache-banned subnets
        $subnet = implode('.', array_slice(explode('.', $ip), 0, 3)) . '.';
        if (Cache::get('blocked_subnet:' . $subnet)) {
            abort(403);
        }

        return $next($request);
    }
}