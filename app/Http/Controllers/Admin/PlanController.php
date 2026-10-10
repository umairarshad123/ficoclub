<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CommasService;
use App\Support\PlanCatalog;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Admin → Plans & Pricing. Edits names, prices, descriptions, bullets, "best for"
 * text, button labels and visibility without touching code (see PlanCatalog).
 *
 * Commas can't change a product's price via its API, so a price change creates
 * a NEW Commas product at that price and switches the plan to it — before the
 * edit is saved, so the website never shows a price Commas wouldn't charge.
 */
class PlanController extends Controller
{
    /** The $1 internal test plan isn't client-editable. */
    private const LOCKED = ['test'];

    public function index()
    {
        $plans = collect(config('plans.plans'))
            ->except(self::LOCKED)
            ->map(fn ($p, $key) => $p + ['key' => $key, 'customized' => PlanCatalog::isCustomized($key)]);

        return view('admin.plans', [
            'plans'     => $plans,
            'surcharge' => PlanCatalog::surchargePercent(),
        ]);
    }

    public function update(Request $request, string $key, CommasService $commas)
    {
        $current = config("plans.plans.$key");
        abort_if(! $current || in_array($key, self::LOCKED, true), 404);

        $data = $request->validate([
            'label'           => 'required|string|max:60',
            'tagline'         => 'nullable|string|max:80',
            'desc'            => 'nullable|string|max:400',
            'amount'          => 'required|numeric|min:1|max:100000',
            'compare_at'      => 'nullable|numeric|min:0|max:100000',
            'period'          => 'nullable|string|max:60',
            'monitoring_note' => 'nullable|string|max:120',
            'billing_note'    => 'nullable|string|max:160',
            'badge'           => 'nullable|string|max:20',
            'cta'             => 'required|string|max:40',
            'features'        => 'required|array|min:1|max:25',
            'features.*'      => 'nullable|string|max:200',
            'best_for'        => 'nullable|string|max:300',
            'visible'         => 'nullable|boolean',
        ]);

        $amount      = round((float) $data['amount'], 2);
        $priceChange = abs($amount - (float) $current['amount']) > 0.009;
        $productId   = $current['commas_product_id'] ?? null;

        if ($priceChange) {
            if (! $commas->isConfigured()) {
                return back()->withInput()->with('error', 'Price not changed: Commas is not connected (COMMAS_API_KEY missing).');
            }

            try {
                $created   = $commas->createProduct($data['label'], $amount, (string) ($data['desc'] ?? ''));
                $productId = basename((string) ($created['payment_link'] ?? '')) ?: null;
            } catch (\Throwable $e) {
                Log::error('[Plans] Could not create Commas product for new price', ['plan' => $key, 'error' => $e->getMessage()]);
                return back()->withInput()->with('error', 'Price not changed — Commas rejected the new product: ' . $e->getMessage());
            }

            if (! $productId) {
                return back()->withInput()->with('error', 'Price not changed — Commas did not return a product link.');
            }

            Log::info('[Plans] Price changed — new Commas product created', [
                'plan' => $key, 'from' => $current['amount'], 'to' => $amount, 'product' => $productId,
            ]);
        }

        PlanCatalog::save($key, [
            'label'             => $data['label'],
            'tagline'           => $data['tagline'] ?? '',
            'desc'              => $data['desc'] ?? '',
            'amount'            => $amount,
            'compare_at'        => $data['compare_at'] ?? null,
            'period'            => $data['period'] ?? '',
            'monitoring_note'   => $data['monitoring_note'] ?? '',
            'billing_note'      => $data['billing_note'] ?? '',
            'badge'             => $data['badge'] ?? null,
            'cta'               => $data['cta'],
            'features'          => $data['features'],
            'best_for'          => $data['best_for'] ?? '',
            'hidden'            => ! $request->boolean('visible'),
            'commas_product_id' => $productId,
        ]);

        return redirect()->to(route('admin.plans') . '#plan-' . $key)->with('success', $priceChange
            ? "{$data['label']} saved — new price \$" . number_format($amount, 2) . ' is live on the website and in Commas.'
            : "{$data['label']} saved — changes are live on the website.");
    }

    public function reset(string $key)
    {
        abort_if(! config("plans.plans.$key") || in_array($key, self::LOCKED, true), 404);

        PlanCatalog::reset($key);

        return redirect()->route('admin.plans')->with('success', 'Plan reset to its original wording and price.');
    }

    public function surcharge(Request $request)
    {
        $data = $request->validate(['surcharge_percent' => 'required|numeric|min:0|max:15']);
        SiteSettings::set('surcharge_percent', round((float) $data['surcharge_percent'], 2));

        return back()->with('success', 'Card processing fee updated to ' . rtrim(rtrim(number_format((float) $data['surcharge_percent'], 2), '0'), '.') . '%.');
    }
}
