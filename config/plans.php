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
|   amount      = one-time charge today (string, 2dp)
|   recurring   = monthly ARB amount AFTER initial charge, or null for none
|   compare_at  = strike-through "was" price, or null
|   save        = dollars saved badge, or null
|   color/btn   = index.blade.php CSS classes (cg/co/cr/cb · btn-g/o/r/b)
|   badge       = ribbon text on the pricing card, or null
|   is_couples  = true → triggers the husband/wife onboarding hub
|
*/

return [

    // Fallback when no/invalid ?plan= is supplied.
    'default' => 'onetime',

    'plans' => [

        // ───────────────────────────── GREEN ─────────────────────────────
        'monthly' => [
            'key'          => 'monthly',
            'label'        => 'Monthly Plan',
            'tag'          => 'MONTHLY PLAN',
            'tagline'      => 'Full 90-Day Credit Transformation',
            'desc'         => 'Full 90-day credit transformation with aggressive 3-bureau disputes. Cancel anytime after 90 days.',
            'amount'       => '197.00',
            'recurring'    => '100.00',
            'compare_at'   => '297.00',
            'save'         => '100',
            'price_big'    => '197',
            'period'       => 'due today, then $100/month',
            'billing_note' => 'then $100/month · cancel after 90 days',
            'sub_note'     => 'Cancel after 90 days',
            'color'        => 'cg',
            'btn'          => 'btn-g',
            'tag_class'    => 'navy-tag',
            'badge'        => null,
            'is_couples'   => false,
            'cta'          => 'Get Started',
            'features'     => [
                'Aggressive 3-bureau disputes (Equifax · Experian · TransUnion)',
                'Collections, charge-offs & late payments challenged',
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
                'Cancel anytime after 90 days — no lock-in',
            ],
        ],

        // ───────────────────────────── ORANGE ────────────────────────────
        'onetime' => [
            'key'          => 'onetime',
            'label'        => 'One-Time Plan',
            'tag'          => 'ONE-TIME PLAN',
            'tagline'      => 'Single Payment · Zero Recurring',
            'desc'         => 'One single payment. Priority dispute filing plus ongoing support and lifetime credit guidance.',
            'amount'       => '697.00',
            'recurring'    => null,
            'compare_at'   => '894.00',
            'save'         => '197',
            'price_big'    => '697',
            'period'       => 'one-time payment',
            'billing_note' => 'one-time payment · zero recurring',
            'sub_note'     => 'Pay once, done',
            'color'        => 'co',
            'btn'          => 'btn-o',
            'tag_class'    => 'gold-tag',
            'badge'        => null,
            'is_couples'   => false,
            'cta'          => 'Pay Once, Done',
            'features'     => [
                'One single payment — zero recurring, ever',
                'Aggressive priority disputes across all 3 bureaus',
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                'Fast-tracked results in 30–45 days',
                '24/7 client portal access',
                'Lifetime credit guidance + funding readiness',
            ],
        ],

        // ───────────────────────────── RED ───────────────────────────────
        'public_records' => [
            'key'          => 'public_records',
            'label'        => 'Public Records Plan',
            'tag'          => 'PUBLIC RECORDS PLAN',
            'tagline'      => 'Factual Disputing · Heavy Items',
            'desc'         => 'Built for the heavy public-records hits: bankruptcies, repossessions, student loans, medical bills and child support — addressed with 3–4 months of factual disputing.',
            'amount'       => '1000.00',
            'recurring'    => null,
            'compare_at'   => null,
            'save'         => null,
            'price_big'    => '1,000',
            'period'       => 'one-time · 3–4 month program',
            'billing_note' => 'one-time payment · 3–4 month program',
            'sub_note'     => 'Heavy public-records work',
            'color'        => 'cr',
            'btn'          => 'btn-r',
            'tag_class'    => 'gold-tag',
            'badge'        => null,
            'is_couples'   => false,
            'cta'          => 'Start Public Records',
            'features'     => [
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                '3–4 months of factual disputing',
                'Aggressive 3-bureau attack (Equifax · Experian · TransUnion)',
                'Dedicated specialist for heavy public-records items',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
            ],
        ],

        // ───────────────────────────── BLUE ──────────────────────────────
        'vip' => [
            'key'          => 'vip',
            'label'        => 'VIP Plan',
            'tag'          => 'VIP PLAN',
            'tagline'      => 'White-Glove · Done-With-You',
            'desc'         => 'Done-with-you. Priority everything, a direct text line to Anthony, and weekly progress calls.',
            'amount'       => '1997.00',
            'recurring'    => null,
            'compare_at'   => null,
            'save'         => null,
            'price_big'    => '1,997',
            'period'       => 'one-time · white-glove',
            'billing_note' => 'one-time · white-glove service',
            'sub_note'     => 'White-glove service',
            'color'        => 'cb',
            'btn'          => 'btn-b',
            'tag_class'    => 'navy-tag',
            'badge'        => '★ VIP',
            'is_couples'   => false,
            'cta'          => 'Apply For VIP',
            'features'     => [
                'Done-with-you white-glove priority service',
                'Direct text line to Anthony — answers in minutes',
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                'Weekly 1-on-1 progress strategy calls',
                '24/7 client portal access',
                'Lender & funding introductions + lifetime guidance',
            ],
        ],

    ],
];
