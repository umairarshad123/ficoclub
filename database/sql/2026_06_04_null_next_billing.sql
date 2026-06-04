-- ---------------------------------------------------------------------------
-- 2026_06_04 · Null next_billing_date on every "one-time" subscription row
-- ---------------------------------------------------------------------------
-- The pre-fix AcceptJsPaymentController unconditionally set
--   next_billing_date = (NOW + 1 month)
-- even when the selected plan had no ARB recurring subscription. The admin
-- subscriptions list therefore showed a fake "next billing" date for
-- customers who will never be billed again (Test, Silver, Gold, Platinum,
-- One-Time, Public Records, VIP, etc.).
--
-- This statement nulls those dates out so the admin renders "—" instead.
-- Rows that DO have a real ARB sub (recurring_amount IS NOT NULL or
-- arb_subscription_id IS NOT NULL) keep their date.
--
-- Idempotent: safe to run multiple times — only touches rows that still
-- have a stale next_billing_date.
-- ---------------------------------------------------------------------------

UPDATE subscriptions
SET    next_billing_date = NULL
WHERE  recurring_amount IS NULL
  AND  arb_subscription_id IS NULL
  AND  next_billing_date IS NOT NULL;
