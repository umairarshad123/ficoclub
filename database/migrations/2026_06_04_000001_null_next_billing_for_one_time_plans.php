<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill: null out next_billing_date on every subscription row that was
 * never actually a recurring billing.
 *
 * The pre-fix AcceptJsPaymentController always set
 *   next_billing_date = now()->addMonth()
 * even when the selected plan had no ARB subscription (one-time / public
 * records / VIP / test / current silver-gold-platinum). The admin
 * subscriptions list was therefore showing a "next billing" date for
 * customers who will never be billed again.
 *
 * Heuristic for "was never recurring":
 *   recurring_amount IS NULL  → never had an ARB subscription
 *   AND arb_subscription_id IS NULL  → never actually wired into Authorize.Net ARB
 *
 * Rows that DO have a real ARB sub keep their date so legacy monthly
 * customers continue to surface correctly in admin.
 */
return new class extends Migration {
    public function up(): void
    {
        DB::table('subscriptions')
            ->whereNull('recurring_amount')
            ->whereNull('arb_subscription_id')
            ->whereNotNull('next_billing_date')
            ->update(['next_billing_date' => null]);
    }

    public function down(): void
    {
        // No-op: rolling back would mean fabricating a billing date, which
        // is exactly the bug this migration fixes.
    }
};
