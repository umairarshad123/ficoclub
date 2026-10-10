<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Admin-editable plan catalog.
 *
 * config/plans.php holds the defaults; edits made on Admin → Plans & Pricing are
 * stored in storage/app/plan-overrides.json (survives deploys) and merged over
 * the defaults at boot (AppServiceProvider), so every config('plans.plans')
 * reader — pricing cards, checkout, onboarding, GHL payloads, dashboards —
 * sees the edited values with no other code changes.
 */
class PlanCatalog
{
    /** Fields the admin can edit. Everything else (colours, key, CSS classes) stays in code. */
    public const EDITABLE = [
        'label', 'tagline', 'desc', 'amount', 'compare_at', 'period', 'billing_note',
        'monitoring_note', 'badge', 'cta', 'features', 'best_for', 'hidden', 'commas_product_id',
    ];

    private static ?array $defaults = null;

    public static function path(): string
    {
        return storage_path('app/plan-overrides.json');
    }

    /** At boot: remember the config/plans.php values, then merge the stored overrides over them. */
    public static function boot(): void
    {
        self::$defaults = config('plans.plans', []);
        self::apply();
    }

    /** Merge stored overrides into config('plans.plans'). */
    public static function apply(): void
    {
        $plans = config('plans.plans', []);

        foreach (self::overrides() as $key => $override) {
            if (! isset($plans[$key]) || ! is_array($override)) {
                continue;
            }
            $plans[$key] = self::derive(array_merge($plans[$key], array_intersect_key($override, array_flip(self::EDITABLE))));
        }

        config(['plans.plans' => $plans]);
    }

    /** Original config/plans.php values (before overrides). */
    public static function defaults(): array
    {
        return self::$defaults ?? config('plans.plans', []);
    }

    public static function overrides(): array
    {
        if (! File::exists(self::path())) {
            return [];
        }
        $data = json_decode((string) File::get(self::path()), true);

        return is_array($data) ? $data : [];
    }

    public static function isCustomized(string $key): bool
    {
        return array_key_exists($key, self::overrides());
    }

    /** Save edits for one plan, then re-apply so this request sees them too. */
    public static function save(string $key, array $values): void
    {
        $all = self::overrides();
        $all[$key] = array_intersect_key($values, array_flip(self::EDITABLE));
        self::write($all);
        config(['plans.plans' => self::defaults()]);
        self::apply();
    }

    /** Drop a plan's edits (back to config/plans.php). */
    public static function reset(string $key): void
    {
        $all = self::overrides();
        unset($all[$key]);
        self::write($all);
        config(['plans.plans' => self::defaults()]);
        self::apply();
    }

    /**
     * Fields that follow from others: the big price and the SAVE badge. Tag text
     * follows the name ("Gold Plan" → "GOLD PLAN").
     */
    public static function derive(array $plan): array
    {
        $amount  = (float) ($plan['amount'] ?? 0);
        $compare = isset($plan['compare_at']) && $plan['compare_at'] !== '' && $plan['compare_at'] !== null ? (float) $plan['compare_at'] : null;

        $plan['amount']     = number_format($amount, 2, '.', '');
        $plan['compare_at'] = $compare ? number_format($compare, 2, '.', '') : null;
        $plan['price_big']  = fmod($amount, 1.0) == 0.0 ? number_format($amount, 0) : number_format($amount, 2);
        $plan['save']       = $compare && $compare > $amount ? rtrim(rtrim(number_format($compare - $amount, 2, '.', ''), '0'), '.') : null;
        $plan['tag']        = strtoupper((string) ($plan['label'] ?? ''));
        $plan['features']   = array_values(array_filter(array_map('trim', (array) ($plan['features'] ?? [])), 'strlen'));
        $plan['hidden']     = ! empty($plan['hidden']);

        return $plan;
    }

    /** Card-processing surcharge Commas adds on top of the price (shown to customers). */
    public static function surchargePercent(): float
    {
        return (float) SiteSettings::get('surcharge_percent', config('payments.surcharge_percent', 4));
    }

    public static function chargedToday(float $amount): float
    {
        return round($amount * (1 + self::surchargePercent() / 100), 2);
    }

    private static function write(array $all): void
    {
        File::ensureDirectoryExists(dirname(self::path()));
        File::put(self::path(), json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
