<?php

/*
|--------------------------------------------------------------------------
| Plan Catalog — SINGLE SOURCE OF TRUTH
|--------------------------------------------------------------------------
|
| Every layer of the app reads pricing from here:
|   - AcceptJsPaymentController  (Authorize.Net charge + ARB recurring)
|   - OnboardingFormController   (post-payment enrollment form)
|   - CouplesController          (legacy two-person flow — kept for historical subs)
|   - resources/views/index.blade.php           (public pricing cards)
|   - resources/views/payments/accept-checkout  (branded checkout sidebar)
|   - Admin DashboardController  (plan-mix analytics)
|
| Change a price ONCE here and it propagates everywhere.
|
| Keys:
|   amount           = one-time charge today (string, 2dp)
|   recurring        = monthly ARB amount AFTER initial charge, or null for none
|   compare_at       = strike-through "was" price, or null
|   save             = dollars saved badge, or null
|   monitoring_note  = small italic line under the price (SmartCredit reminder)
|   best_for         = short positioning blurb shown above the CTA
|   color/btn        = index.blade.php CSS classes (silver-card / gold-card / platinum-card · btn-light / btn-gold)
|   badge            = corner ribbon text on the pricing card, or null
|   is_couples       = legacy flag, false on all current plans
|   hidden           = never shown on the pricing grid; only buyable by someone
|                      holding the maintenance preview cookie (live $1 testing)
|   commas_product_id = Commas product hashid (differs sandbox vs production →
|                      set per environment in .env; create with `php artisan commas:setup-products`)
|
| Plan order in this file = card order on the pricing grid.
|
*/

return [

    // Fallback when no/invalid ?plan= is supplied.
    'default' => 'membership',

    'plans' => [

        // ───────────────────────────── SILVER ───────────────────────────
        'silver' => [
            'key'             => 'silver',
            'hidden'          => true,   // retired Oct 2026 — replaced by membership / fast-track
            'commas_product_id' => env('COMMAS_PRODUCT_SILVER'),
            'label'           => 'Silver Plan',
            'tag'             => 'SILVER PLAN',
            'tagline'         => 'Single Program Fee',
            'desc'            => 'A single program fee. Aggressive 3-bureau disputing across late payments, collections, charge-offs and inquiries.',
            'amount'          => '697.00',
            'recurring'       => null,
            'compare_at'      => '894.00',
            'save'            => '197',
            'price_big'       => '697',
            'period'          => 'single program fee',
            'billing_note'    => 'single program fee · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'Single program fee',
            'color'           => 'silver-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'silver-tag',
            'badge'           => null,
            'is_couples'      => false,
            'cta'             => 'Apply For Silver',
            'features'        => [
                'Personal information update',
                'Late payments challenged',
                'Collections challenged',
                'Charge-offs challenged',
                'Hard inquiries challenged',
                'Fast-tracked results in 30–90 days',
                '3-bureau dispute letters prepared on your behalf',
                'FCRA & FDCPA-based dispute strategy',
                'Credit-building guidance after disputes',
                'Email & SMS status updates',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
            ],
            'best_for'        => 'Clients with collections, charge-offs, late payments, or inquiries looking for a clean, fast start.',
        ],

        // ───────────────────────────── GOLD ─────────────────────────────
        'gold' => [
            'key'             => 'gold',
            'hidden'          => true,   // retired Oct 2026 — replaced by membership / fast-track
            'commas_product_id' => env('COMMAS_PRODUCT_GOLD'),
            'label'           => 'Gold Plan',
            'tag'             => 'GOLD PLAN',
            'tagline'         => 'Public Records Program',
            'desc'            => 'Focused on the heavy public-records hits: bankruptcies, repossessions, student loans, medical bills and child support.',
            'amount'          => '897.00',
            'recurring'       => null,
            'compare_at'      => '1097.00',
            'save'            => '200',
            'price_big'       => '897',
            'period'          => 'one-time program fee',
            'billing_note'    => 'one-time program fee · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'Public records focus',
            'color'           => 'gold-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'gold-tag',
            'badge'           => null,
            'is_couples'      => false,
            'cta'             => 'Apply For Gold',
            'features'        => [
                'Bankruptcy records challenged',
                'Repossessions challenged',
                'Student loans challenged',
                'Medical bills challenged',
                'Child support challenged',
                'Everything in Silver, plus public records coverage',
                'FCRA Metro 2 compliance-based dispute strategy',
                'Debt validation letters to collection agencies',
                'Goodwill letter assistance for paid late payments',
                'Credit utilization guidance & score optimization',
                'Tradeline strategy consultation',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
            ],
            'best_for'        => 'Clients with bankruptcies, repos, student loans, or medical debt who need deeper public records work.',
        ],

        // ───────────────────────────── PLATINUM ─────────────────────────
        'platinum' => [
            'key'             => 'platinum',
            'hidden'          => true,   // retired Oct 2026 — replaced by membership / fast-track
            'commas_product_id' => env('COMMAS_PRODUCT_PLATINUM'),
            'label'           => 'Platinum Plan',
            'tag'             => 'PLATINUM PLAN',
            'tagline'         => 'White-Glove · Done-For-You',
            'desc'            => 'Everything. Done for you. A dedicated specialist, front-of-line priority disputes, and lender introductions.',
            'amount'          => '1497.00',
            'recurring'       => null,
            'compare_at'      => '1997.00',
            'save'            => '500',
            'price_big'       => '1,497',
            'period'          => 'white-glove program',
            'billing_note'    => 'white-glove program · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'White-glove service',
            'color'           => 'platinum-card',
            'btn'             => 'btn-gold',
            'tag_class'       => 'platinum-tag',
            'badge'           => '+ VIP',
            'is_couples'      => false,
            'cta'             => 'Apply For Platinum',
            'features'        => [
                'Everything included — late payments, collections, charge-offs, repossessions, bankruptcy, student loans, medical bills & child support',
                'Done-for-you service — we handle everything, you do nothing',
                'Dedicated credit specialist assigned exclusively to your case',
                'Front-of-line priority disputes — fastest turnaround of any plan',
                'Identity theft resolution support',
                'Rapid rescore coordination with lenders',
                'Access to preferred funding partners & credit lines',
                'Direct advisor access during business hours',
                '24/7 VIP client portal + real-time score tracking',
                'Exclusive lender & funding introductions — not available on other plans',
            ],
            'best_for'        => 'Clients preparing for a mortgage, major funding, or who want a dedicated specialist handling everything start to finish.',
        ],

        // ───────────────────────────── $2,000 ────────────────────────────
        'signature' => [
            'key'             => 'signature',
            'hidden'          => true,   // retired Oct 2026 — replaced by membership / fast-track
            'commas_product_id' => env('COMMAS_PRODUCT_SIGNATURE'),
            'label'           => 'Signature Plan',
            'tag'             => 'SIGNATURE PLAN',
            'tagline'         => 'Complete Credit Overhaul',
            'desc'            => 'Our most complete credit program. One single payment covering the full scope of disputes, public-records work and lifetime guidance.',
            'amount'          => '2000.00',
            'recurring'       => null,
            'compare_at'      => null,
            'save'            => null,
            'price_big'       => '2,000',
            'period'          => 'complete program',
            'billing_note'    => 'complete program · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'Complete program',
            'color'           => 'platinum-card',
            'btn'             => 'btn-gold',
            'tag_class'       => 'platinum-tag',
            'badge'           => null,
            'is_couples'      => false,
            'cta'             => 'Apply For Signature',
            'features'        => [
                'Everything included — full disputes across all 3 bureaus',
                'Done-for-you service — we handle everything, you do nothing',
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                'Dedicated credit specialist assigned exclusively to your case',
                'Front-of-line priority disputes — fastest turnaround',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access + lifetime credit guidance',
            ],
            'best_for'        => 'Clients who want the most complete, fully done-for-you program with lifetime guidance.',
        ],

        // ───────────────────────── 850 CLUB MEMBERSHIP ──────────────────
        // Checkout runs on the GHL page (Authorize.Net) via checkout_url, not /accept-checkout.
        'membership' => [
            'key'             => 'membership',
            'checkout_url'    => 'https://packages.850ficoclub.com/checkout/membership',
            'label'           => '850 Club Membership',
            'tag'             => '850 CLUB MEMBERSHIP',
            'tagline'         => 'Monthly Plan',
            'desc'            => 'Full-service credit restoration, round after round, until you reach your goal.',
            'amount'          => '149.00',
            'recurring'       => '149.00',
            'compare_at'      => null,
            'save'            => null,
            'price_big'       => '149',
            'period'          => '/month',
            'billing_note'    => '/month · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'Monthly plan',
            'color'           => 'gold-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'gold-tag',
            'badge'           => 'MOST POPULAR',
            'is_couples'      => false,
            'cta'             => 'Join the Club',
            'features'        => [
                'Disputes sent to Equifax, Experian & TransUnion every 30–35 days',
                'Unlimited items challenged: late payments, collections, charge-offs, inquiries & public records',
                'Debt validation & goodwill letters sent to your creditors',
                'Monthly progress report + score review',
                'Rent reporting setup to add positive payment history',
                'Authorized user tradeline strategy guidance',
                "Lender handoff when you're mortgage or auto ready",
                '24/7 client portal access',
            ],
            'best_for'        => 'Full-service credit restoration, no setup fee. Cancel anytime. Hit your goal? Stay in the Club on Club Maintenance for $49/mo.',
        ],

        // ───────────────────────── PLATINUM FAST TRACK ──────────────────
        'fast-track' => [
            'key'             => 'fast-track',
            'checkout_url'    => 'https://packages.850ficoclub.com/checkout/fast-track',
            'label'           => 'Platinum Fast Track',
            'tag'             => 'PLATINUM FAST TRACK',
            'tagline'         => 'One-Time Payment',
            'desc'            => 'An accelerated 90-day program for clients on a deadline: buying a home, financing a car, or refinancing.',
            'amount'          => '1497.00',
            'recurring'       => null,
            'compare_at'      => null,
            'save'            => null,
            'price_big'       => '1,497',
            'period'          => 'one-time',
            'billing_note'    => 'one-time · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'One-time payment',
            'color'           => 'platinum-card',
            'btn'             => 'btn-gold',
            'tag_class'       => 'platinum-tag',
            'badge'           => 'VIP',
            'is_couples'      => false,
            'cta'             => 'Start Fast Track',
            'features'        => [
                '3 accelerated dispute rounds across all 3 bureaus',
                'Every negative item type challenged, including public records',
                'Direct creditor interventions: debt validation & goodwill requests',
                'Dedicated credit specialist assigned to your file',
                'Front-of-line priority processing',
                'Custom mortgage-readiness plan',
                'Direct coordination with your loan officer',
                '24/7 VIP client portal + live score tracking',
            ],
            'best_for'        => 'Clients on a deadline. One payment, no monthly service fee.',
        ],

        // ───────────────────────────── $1 TEST (hidden) ──────────────────
        // Live end-to-end testing of the Commas checkout. Not on the pricing
        // grid; /accept-checkout?plan=test only works with the maintenance
        // preview cookie (see App\Http\Middleware\SiteMaintenance). Remove after launch.
        'test' => [
            'key'             => 'test',
            'commas_product_id' => env('COMMAS_PRODUCT_TEST'),
            'hidden'          => true,
            'label'           => 'Test Plan',
            'tag'             => 'INTERNAL TEST',
            'tagline'         => 'Internal checkout test',
            'desc'            => 'Internal $1 plan for testing the live checkout end to end. Not visible to customers.',
            'amount'          => '1.00',
            'recurring'       => null,
            'compare_at'      => null,
            'save'            => null,
            'price_big'       => '1',
            'period'          => 'internal test',
            'billing_note'    => 'internal test — refund after verifying',
            'monitoring_note' => '',
            'sub_note'        => 'Internal test',
            'color'           => 'silver-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'silver-tag',
            'badge'           => null,
            'is_couples'      => false,
            'cta'             => 'Test Checkout',
            'features'        => ['Live payment test', 'GHL webhook test', 'Onboarding handoff test'],
            'best_for'        => 'Internal testing only.',
        ],

    ],
];
