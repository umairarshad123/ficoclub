<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Couples Glow-Up — two-person onboarding flow.
 *
 *   Hub  →  Husband onboarding  →  Hub  →  Wife onboarding  →  Done
 *
 * Each partner is submitted to the DisputeFox CRM as a separate customer.
 * Progress is tracked in the session (set after a verified couples payment).
 */
class CouplesController extends Controller
{
    /** Gate: a confirmed couples payment must exist. */
    private function guard(Request $request)
    {
        $paymentSuccess = session('acceptjs_payment_success');
        $invoiceNumber  = session('acceptjs_invoice_number');
        $cacheData      = $invoiceNumber ? Cache::get('checkout_customer_' . $invoiceNumber) : null;

        $planKey   = $cacheData['plan_key'] ?? null;
        $catalog   = config('plans.plans');
        $isCouples = ($planKey && ($catalog[$planKey]['is_couples'] ?? false))
                     || session('couples_flow') === true;

        if ((!$paymentSuccess && !$cacheData) || !$isCouples) {
            Log::warning('Couples hub access denied', [
                'ip'         => $request->ip(),
                'session_id' => session()->getId(),
                'plan_key'   => $planKey,
            ]);
            return redirect()->route('accept.checkout')
                ->with('error', 'Please complete the Couples Plan payment first.');
        }

        return null; // access granted
    }

    /** The hub page with the two partner cards + progress. */
    public function hub(Request $request)
    {
        if ($redirect = $this->guard($request)) {
            return $redirect;
        }

        $husbandDone = (bool) session('couples_husband_done', false);
        $wifeDone    = (bool) session('couples_wife_done', false);
        $bothDone    = $husbandDone && $wifeDone;

        $invoiceNumber = session('acceptjs_invoice_number');
        $cacheData     = $invoiceNumber ? Cache::get('checkout_customer_' . $invoiceNumber) : [];
        $buyerName     = trim(($cacheData['first_name'] ?? '') . ' ' . ($cacheData['last_name'] ?? ''));

        Log::info('Couples hub rendered', [
            'invoice'      => $invoiceNumber,
            'husband_done' => $husbandDone,
            'wife_done'    => $wifeDone,
        ]);

        return view('couples-hub', compact(
            'husbandDone',
            'wifeDone',
            'bothDone',
            'buyerName',
            'invoiceNumber'
        ));
    }

    /**
     * Marks a partner's onboarding as complete.
     * Called by the onboarding form's JS right after a successful
     * DisputeFox submission, then it redirects back to the hub.
     */
    public function complete(Request $request)
    {
        if ($this->guard($request)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
        }

        $partner = strtolower((string) $request->input('partner'));
        if (!in_array($partner, ['husband', 'wife'], true)) {
            return response()->json(['ok' => false, 'message' => 'Invalid partner'], 422);
        }

        session(['couples_' . $partner . '_done' => true]);

        $husbandDone = (bool) session('couples_husband_done', false);
        $wifeDone    = (bool) session('couples_wife_done', false);

        Log::info('Couples partner onboarding completed', [
            'invoice'      => session('acceptjs_invoice_number'),
            'partner'      => $partner,
            'husband_done' => $husbandDone,
            'wife_done'    => $wifeDone,
        ]);

        return response()->json([
            'ok'           => true,
            'partner'      => $partner,
            'husband_done' => $husbandDone,
            'wife_done'    => $wifeDone,
            'both_done'    => $husbandDone && $wifeDone,
            'redirect'     => route('couples.hub'),
        ]);
    }
}
