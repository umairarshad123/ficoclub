<?php

/*
|--------------------------------------------------------------------------
| Site Maintenance Gate
|--------------------------------------------------------------------------
|
| While enabled, every public page, form POST and checkout returns the
| "We'll be back soon" page (HTTP 503). Enforced by
| App\Http\Middleware\SiteMaintenance.
|
| Defaults to ON so a normal cPanel deploy activates it — no server-side
| artisan command needed. To turn it off, either set SITE_MAINTENANCE=false
| in the live .env, or flip the default below and deploy.
|
| Team preview: set SITE_MAINTENANCE_SECRET in .env, then visit
|   https://850ficoclub.com/?preview=<secret>
| once — a cookie lets that browser see the real site for 7 days.
|
*/

return [

    'enabled' => (bool) env('SITE_MAINTENANCE', true),

    'secret' => env('SITE_MAINTENANCE_SECRET'),

    // Paths that keep working during maintenance (Request::is() patterns).
    'except' => [
        'admin',
        'admin/*',                  // admin dashboard + login
        'webhooks/*',               // Authorize.Net (and later Commas) webhooks
        '.well-known/*',            // Apple Pay domain verification, SSL challenges
        'funding/download',         // staff download links for lead uploads
        'up',                       // health check
    ],

];
