<?php

namespace App\Console\Commands;

use App\Services\CommasService;
use Illuminate\Console\Command;

/**
 * One-time Commas setup, run once per environment (sandbox, then production):
 *
 *   php artisan commas:setup products   # creates one product per plan, prints .env lines
 *   php artisan commas:setup webhook    # registers /webhooks/commas, prints the signing secret
 *   php artisan commas:setup check      # verifies key, slug, products and webhook config
 */
class CommasSetupCommand extends Command
{
    protected $signature   = 'commas:setup {step : products | webhook | check} {--url= : Webhook URL (default: APP_URL/webhooks/commas)}';
    protected $description = 'Create Commas products / register the webhook / verify configuration';

    public function handle(CommasService $commas): int
    {
        if (! $commas->isConfigured()) {
            $this->error('Set COMMAS_API_KEY (and COMMAS_ENVIRONMENT) in .env first.');
            return self::FAILURE;
        }

        $this->line('Environment: <info>' . $commas->environment() . '</info>');

        return match ($this->argument('step')) {
            'products' => $this->products($commas),
            'webhook'  => $this->webhook($commas),
            'check'    => $this->check($commas),
            default    => tap(self::FAILURE, fn () => $this->error('Step must be products, webhook or check.')),
        };
    }

    private function products(CommasService $commas): int
    {
        $existing = collect($commas->listProducts())->keyBy(fn ($p) => strtolower((string) ($p['title'] ?? '')));
        $envLines = [];

        foreach (config('plans.plans') as $key => $plan) {
            $envKey = 'COMMAS_PRODUCT_' . strtoupper($key);

            if (! empty($plan['commas_product_id'])) {
                $this->line("  {$plan['label']}: already set ({$plan['commas_product_id']})");
                continue;
            }

            $match = $existing->get(strtolower($plan['label']));
            if ($match) {
                $this->warn("  {$plan['label']}: a product with this title already exists — reusing it");
                $envLines[] = "{$envKey}=" . $match['id'];
                continue;
            }

            $created = $commas->createProduct($plan['label'], (float) $plan['amount'], $plan['desc']);
            // The hashid the SDK needs is the last segment of the payment link.
            $hashid  = basename((string) ($created['payment_link'] ?? ''));
            $this->info("  {$plan['label']}: created \${$plan['amount']} → {$hashid} (numeric {$created['product_id']})");
            $envLines[] = "{$envKey}={$hashid}";
        }

        if ($envLines) {
            $this->newLine();
            $this->line('Add these to .env, then run `php artisan config:clear`:');
            foreach ($envLines as $l) {
                $this->line('  ' . $l);
            }
        }

        return self::SUCCESS;
    }

    private function webhook(CommasService $commas): int
    {
        $url = $this->option('url') ?: rtrim((string) config('app.url'), '/') . '/webhooks/commas';

        foreach ($commas->listWebhookSubscriptions() as $sub) {
            if (($sub['webhook_url'] ?? null) === $url) {
                $this->warn("Already registered (id {$sub['id']}) for {$url}.");
                $this->line('The signing secret is only shown at creation. If COMMAS_WEBHOOK_SECRET is lost,');
                $this->line('delete that subscription in the Commas dashboard and run this again.');
                return self::SUCCESS;
            }
        }

        $created = $commas->createWebhookSubscription($url);

        $this->info("Registered {$url} (id {$created['id']}) for: " . implode(', ', $created['event_types'] ?? []));
        $this->newLine();
        $this->line('Add this to .env, then run `php artisan config:clear`:');
        $this->line('  COMMAS_WEBHOOK_SECRET=' . ($created['secret_key'] ?? '(missing — check the Commas dashboard)'));

        return self::SUCCESS;
    }

    private function check(CommasService $commas): int
    {
        $ok = true;

        $products = collect($commas->listProducts())->keyBy('id');
        $this->info('✓ API key works (' . $products->count() . ' products visible)');

        if (config('services.commas.creator_slug')) {
            $this->info('✓ COMMAS_CREATOR_SLUG = ' . config('services.commas.creator_slug'));
        } else {
            $this->error('✗ COMMAS_CREATOR_SLUG is not set');
            $ok = false;
        }

        foreach (config('plans.plans') as $key => $plan) {
            $id = $plan['commas_product_id'] ?? null;
            $remote = $id ? $products->get($id) : null;

            if (! $id) {
                $this->error("✗ {$plan['label']}: COMMAS_PRODUCT_" . strtoupper($key) . ' not set');
                $ok = false;
            } elseif (! $remote) {
                $this->error("✗ {$plan['label']}: product {$id} not found in Commas");
                $ok = false;
            } elseif (abs((float) $remote['price'] - (float) $plan['amount']) > 0.009) {
                $this->error("✗ {$plan['label']}: Commas price \${$remote['price']} ≠ site price \${$plan['amount']}");
                $ok = false;
            } else {
                $this->info("✓ {$plan['label']}: {$id} at \${$remote['price']}");
            }
        }

        if (config('services.commas.webhook_secret')) {
            $this->info('✓ COMMAS_WEBHOOK_SECRET set');
        } else {
            $this->error('✗ COMMAS_WEBHOOK_SECRET not set — run `php artisan commas:setup webhook`');
            $ok = false;
        }

        $this->line('PAYMENT_PROVIDER = ' . config('payments.provider'));

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
