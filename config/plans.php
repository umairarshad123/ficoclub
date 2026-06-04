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

    ],
];
