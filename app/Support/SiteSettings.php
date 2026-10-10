<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Settings the admin can change from the Website page without touching .env.
 * Stored as JSON in storage/app (not the cache — the cPanel deploy runs
 * cache:clear, which would silently undo an admin's choice).
 *
 * A stored value overrides the matching config default; null = "use .env".
 */
class SiteSettings
{
    private static ?array $cache = null;

    public static function path(): string
    {
        return storage_path('app/site-settings.json');
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $all = self::all();
        $all[$key] = $value;
        File::ensureDirectoryExists(dirname(self::path()));
        File::put(self::path(), json_encode($all, JSON_PRETTY_PRINT));
        self::$cache = $all;
    }

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $data = File::exists(self::path()) ? json_decode((string) File::get(self::path()), true) : null;

        return self::$cache = is_array($data) ? $data : [];
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    /** Maintenance mode: admin toggle if set, otherwise SITE_MAINTENANCE from .env. */
    public static function maintenanceEnabled(): bool
    {
        $override = self::get('maintenance');

        return $override === null ? (bool) config('maintenance.enabled') : (bool) $override;
    }
}
