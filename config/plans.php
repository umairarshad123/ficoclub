<?php

/*
|--------------------------------------------------------------------------
| Plan Catalog — SINGLE SOURCE OF TRUTH
|--------------------------------------------------------------------------
|
| Every layer of the app reads pricing from here:
|   - AcceptJsPaymentController  (Authorize.Net charge + ARB recurring)
|   - OnboardingFormController   (post-payment enrollment form)
|   - CouplesController          (couples 2-person flow)
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
                'Full 90-day credit transformation roadmap',
                'Aggressive 3-bureau disputes (Equifax · Experian · TransUnion)',
                'Collections, charge-offs & late payments challenged',
                'Monthly progress reports + live score tracking',
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
            'amount'       => '497.00',
            'recurring'    => null,
            'compare_at'   => '694.00',
            'save'         => '197',
            'price_big'    => '497',
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
                'Fast-tracked results in 30–45 days',
                'Ongoing support until items are resolved',
                'Lifetime credit guidance + funding readiness',
            ],
        ],

        // ───────────────────────────── RED ───────────────────────────────
        'couples' => [
            'key'          => 'couples',
            'label'        => 'Couples Plan',
            'tag'          => 'COUPLES PLAN',
            'tagline'      => '2 Plans · 1 Price',
            'desc'         => 'For two. A coordinated 3-bureau attack so you and your partner restore credit and prep funding together.',
            'amount'       => '900.00',
            'recurring'    => null,
            'compare_at'   => null,
            'save'         => null,
            'price_big'    => '900',
            'period'       => '2 plans, 1 price',
            'billing_note' => '2 plans, 1 price · one-time',
            'sub_note'     => 'For two partners',
            'color'        => 'cr',
            'btn'          => 'btn-r',
            'tag_class'    => 'gold-tag',
            'badge'        => null,
            'is_couples'   => true,
            'cta'          => 'Apply As Couple',
            'features'     => [
                'Two complete credit programs — one combined price',
                'Aggressive dual 3-bureau restoration for both partners',
                'Coordinated, synchronized bureau-attack strategy',
                'Joint funding & mortgage-readiness preparation',
                'Shared progress dashboard for both partners',
            ],
        ],

        // ───────────────────────────── BLUE (NEW) ────────────────────────
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
                'Weekly 1-on-1 progress strategy calls',
                'Aggressive priority disputes + escalation handling',
                'Lender & funding introductions + lifetime guidance',
            ],
        ],

    ],
];
