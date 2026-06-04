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
|   color/btn        = index.blade.php CSS classes (gold-card / silver-card / platinum-card · btn-light / btn-gold)
|   badge            = corner ribbon text on the pricing card, or null
|   is_couples       = legacy flag, false on all current plans
|
| Plan order in this file = card order on the pricing grid.
|
*/

return [

    // Fallback when no/invalid ?plan= is supplied.
    'default' => 'silver',

    'plans' => [

        // ───────────────────────────── SILVER ───────────────────────────
        'silver' => [
            'key'             => 'silver',
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
            'cta'             => 'Start My Program',
            'features'        => [
                'Personal information update',
                'Late payments challenged',
                'Collections challenged',
                'Charge-offs challenged',
                'Hard inquiries challenged',
                'Fast-tracked results in 30–90 days',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
            ],
        ],

        // ───────────────────────────── GOLD ─────────────────────────────
        'gold' => [
            'key'             => 'gold',
            'label'           => 'Gold Plan',
            'tag'             => 'GOLD PLAN',
            'tagline'         => 'Public Records Program',
            'desc'            => 'Focused on the heavy public-records hits: bankruptcies, repossessions, student loans, medical bills and child support.',
            'amount'          => '997.00',
            'recurring'       => null,
            'compare_at'      => '1197.00',
            'save'            => '200',
            'price_big'       => '997',
            'period'          => 'one-time program fee',
            'billing_note'    => 'one-time program fee · + $34.95/mo SmartCredit monitoring',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring',
            'sub_note'        => 'Public records focus',
            'color'           => 'gold-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'gold-tag',
            'badge'           => null,
            'is_couples'      => false,
            'cta'             => 'Start My Program',
            'features'        => [
                'Bankruptcy records challenged',
                'Repossessions challenged',
                'Student loans challenged',
                'Medical bills challenged',
                'Child support challenged',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
            ],
        ],

        // ───────────────────────────── PLATINUM ─────────────────────────
        'platinum' => [
            'key'             => 'platinum',
            'label'           => 'Platinum Plan',
            'tag'             => 'PLATINUM PLAN',
            'tagline'         => 'White-Glove · Done-For-You',
            'desc'            => 'Everything. Done for you. A dedicated specialist, front-of-line priority disputes, and lender introductions.',
            'amount'          => '1997.00',
            'recurring'       => null,
            'compare_at'      => '2497.00',
            'save'            => '500',
            'price_big'       => '1,997',
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
                '1-on-1 strategy calls to track progress & plan next steps',
                '24/7 VIP client portal + real-time score tracking',
                'Exclusive lender & funding introductions — not available on other plans',
            ],
        ],

        // ───────────────────────────── TEST · $5 ────────────────────────
        // Internal verification charge. Visible card on the pricing grid so
        // staff can run a live end-to-end Authorize.Net + webhook test.
        // NOT for customer use — UI labels clearly say so.
        'test' => [
            'key'             => 'test',
            'label'           => 'Test Plan',
            'tag'             => 'INTERNAL TEST',
            'tagline'         => 'DO NOT PURCHASE',
            'desc'            => 'Internal $5 verification charge. Used by staff to confirm Authorize.Net, webhooks, and onboarding work end-to-end.',
            'amount'          => '5.00',
            'recurring'       => null,
            'compare_at'      => null,
            'save'            => null,
            'price_big'       => '5',
            'period'          => 'verification charge · DO NOT PURCHASE',
            'billing_note'    => 'internal $5 verification charge',
            'monitoring_note' => null,
            'sub_note'        => 'Staff use only',
            'color'           => 'test-card',
            'btn'             => 'btn-light',
            'tag_class'       => 'test-tag',
            'badge'           => 'TEST',
            'is_couples'      => false,
            'cta'             => 'Run Test Charge',
            'features'        => [
                'Charges exactly $5.00 via Authorize.Net',
                'Confirms end-to-end payment flow + webhooks',
                'Verifies GHL + Meta CAPI fire correctly',
                'Internal staff verification — DO NOT PURCHASE',
            ],
        ],

    ],
];
