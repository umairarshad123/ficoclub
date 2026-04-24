<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OnboardingFormController extends Controller
{
    /**
     * Plan map — must mirror AcceptJsPaymentController exactly.
     * This is the single source of truth for plan labels & amounts
     * used when rendering the onboarding form.
     */
    
    private $planMap = [
        'silver'   => ['label' => 'Silver Membership',   'amount' => '299.00'],
        'gold'     => ['label' => 'Gold Membership',     'amount' => '399.00'],
        'platinum' => ['label' => 'Platinum Membership', 'amount' => '499.00'],
    ];

    public function show(Request $request)
    {
        // ── 1. Read payment metadata from session (set by AcceptJsPaymentController) ──
        $paymentSuccess = session('acceptjs_payment_success');
        $invoiceNumber  = session('acceptjs_invoice_number');
        $transactionId  = session('acceptjs_transaction_id');
        $customer       = session('acceptjs_customer', []);

        // ── 2. Fallback: check the 60-min cache keyed by invoice ──
        $cacheData = null;
        if ($invoiceNumber) {
            $cacheData = Cache::get('checkout_customer_' . $invoiceNumber);
        }

        // ── 3. Server-side access control ──
        //    Must have a confirmed payment before showing the onboarding form.
        if (!$paymentSuccess && !$cacheData) {
            Log::warning('Onboarding form access denied — no valid payment found', [
                'ip'         => $request->ip(),
                'session_id' => session()->getId(),
            ]);

            return redirect()->route('accept.checkout')
                ->with('error', 'Please complete your payment before accessing the enrollment form.');
        }

        // ── 4. Resolve plan key & derive display values ──
        //    Cache is authoritative (written right after successful charge).
        //    Session is secondary (also written at same time).
        $planKey   = $cacheData['plan_key']   ?? null;
        $planLabel = $cacheData['plan_label'] ?? null;
        $amount    = $cacheData['amount']      ?? null;

        // Validate planKey against known plans; default to platinum if stale/missing
        if (!$planKey || !isset($this->planMap[$planKey])) {
            $planKey   = 'platinum';
            $planLabel = $this->planMap['platinum']['label'];
            $amount    = $this->planMap['platinum']['amount'];
        }

        // Always derive label & amount from the server-side map so they cannot
        // be manipulated by editing session/cache values directly.
        $planLabel = $this->planMap[$planKey]['label'];
        $amount    = $this->planMap[$planKey]['amount'];

        // ── 5. Customer prefill data ──
        $firstName   = $cacheData['first_name'] ?? $customer['first_name'] ?? '';
        $lastName    = $cacheData['last_name']  ?? $customer['last_name']  ?? '';
        $email       = $cacheData['email']       ?? $customer['email']       ?? '';
        $phone       = $cacheData['phone']       ?? $customer['phone']       ?? '';
        $address     = $cacheData['address']     ?? $customer['address']     ?? '';
        $city        = $cacheData['city']        ?? $customer['city']        ?? '';
        $state       = $cacheData['state']       ?? $customer['state']       ?? '';
        $zip         = $cacheData['zip']         ?? $customer['zip']         ?? '';

        Log::info('Onboarding form rendered', [
            'ip'             => $request->ip(),
            'invoice'        => $invoiceNumber,
            'plan_key'       => $planKey,
            'plan_label'     => $planLabel,
            'amount'         => $amount,
            'email'          => $email,
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
            'transactionId'
        ));
    }
}