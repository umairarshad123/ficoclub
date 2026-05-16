<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OnboardingFormController extends Controller
{
    public function show(Request $request)
    {
        // Plan catalog — single source of truth (config/plans.php)
        $planCatalog = config('plans.plans');
        $defaultPlan = config('plans.default', 'onetime');

        // ── 1. Read payment metadata from session (set by AcceptJsPaymentController) ──
        $paymentSuccess = session('acceptjs_payment_success');
        $invoiceNumber  = session('acceptjs_invoice_number');
        $transactionId  = session('acceptjs_transaction_id');
        $customer       = session('acceptjs_customer', []);

        // ── 2. Fallback: check the 120-min cache keyed by invoice ──
        $cacheData = null;
        if ($invoiceNumber) {
            $cacheData = Cache::get('checkout_customer_' . $invoiceNumber);
        }

        // ── 3. Server-side access control ──
        if (!$paymentSuccess && !$cacheData) {
            Log::warning('Onboarding form access denied — no valid payment found', [
                'ip'         => $request->ip(),
                'session_id' => session()->getId(),
            ]);

            return redirect()->route('accept.checkout')
                ->with('error', 'Please complete your payment before accessing the enrollment form.');
        }

        // ── 4. Resolve plan key & derive display values (server-side authoritative) ──
        $planKey = $cacheData['plan_key'] ?? null;

        if (!$planKey || !isset($planCatalog[$planKey])) {
            $planKey = $defaultPlan;
        }

        $planLabel = $planCatalog[$planKey]['label'];
        $amount    = $planCatalog[$planKey]['amount'];

        // ── 5. Couples partner context ──
        //    partner = husband | wife (only meaningful for the couples plan)
        $isCouples = (bool) ($planCatalog[$planKey]['is_couples'] ?? false)
                     || session('couples_flow') === true;
        $partner   = strtolower((string) $request->query('partner', ''));
        if (!in_array($partner, ['husband', 'wife'], true)) {
            $partner = '';
        }

        if ($isCouples && $partner === '') {
            // Couples buyers must go through the hub, not the bare form.
            return redirect()->route('couples.hub');
        }

        // Distinguish each partner inside the external CRM.
        if ($isCouples && $partner !== '') {
            $planLabel = $planCatalog[$planKey]['label'] . ' — ' . ucfirst($partner);
        }

        // ── 6. Customer prefill ──
        //    Husband = the buyer (prefill).  Wife = blank (different person).
        $prefill = ($isCouples && $partner === 'wife') ? false : true;

        $firstName = $prefill ? ($cacheData['first_name'] ?? $customer['first_name'] ?? '') : '';
        $lastName  = $prefill ? ($cacheData['last_name']  ?? $customer['last_name']  ?? '') : '';
        $email     = $prefill ? ($cacheData['email']      ?? $customer['email']      ?? '') : '';
        $phone     = $prefill ? ($cacheData['phone']      ?? $customer['phone']      ?? '') : '';
        $address   = $prefill ? ($cacheData['address']    ?? $customer['address']    ?? '') : '';
        $city      = $prefill ? ($cacheData['city']       ?? $customer['city']       ?? '') : '';
        $state     = $prefill ? ($cacheData['state']      ?? $customer['state']      ?? '') : '';
        $zip       = $prefill ? ($cacheData['zip']        ?? $customer['zip']        ?? '') : '';

        Log::info('Onboarding form rendered', [
            'ip'         => $request->ip(),
            'invoice'    => $invoiceNumber,
            'plan_key'   => $planKey,
            'plan_label' => $planLabel,
            'amount'     => $amount,
            'is_couples' => $isCouples,
            'partner'    => $partner,
            'email'      => $email,
        ]);

        return view('onboardingform', compact(
            'planKey',
            'planLabel',
            'amount',
            'firstName',
            'lastName',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'zip',
            'invoiceNumber',
            'transactionId',
            'isCouples',
            'partner'
        ));
    }
}
