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
            'tagline'      => 'Ongoing Credit Transformation',
            'desc'         => 'Ongoing month-to-month credit transformation with aggressive 3-bureau disputes. Your membership continues for life until you cancel — cancel anytime.',
            'amount'       => '197.00',
            'recurring'    => '100.00',
            'compare_at'   => '297.00',
            'save'         => '100',
            'price_big'    => '197',
            'period'       => '$197 enrollment, then $100/month',
            'billing_note' => 'then $100/month · continues until cancelled',
            'sub_note'     => 'Cancel anytime',
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
                'Unlimited disputes every billing cycle — no caps',
                'Ongoing month-to-month service — continues for life until cancelled',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
                'Cancel anytime — no contracts, no lock-in',
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
            'period'       => 'single program fee',
            'billing_note' => 'single program fee · zero recurring',
            'sub_note'     => 'No recurring fees',
            'color'        => 'co',
            'btn'          => 'btn-o',
            'tag_class'    => 'gold-tag',
            'badge'        => null,
            'is_couples'   => false,
            'cta'          => 'Start My Program',
            'features'     => [
                'One single payment — zero recurring, ever',
                'Aggressive priority disputes across all 3 bureaus',
                'Collections, charge-offs & late payments challenged',
                'Public records: bankruptcies, repossessions, student loans, medical bills & child support addressed',
                'Fast-tracked results in 30–45 days',
                'Monthly progress reports + live score tracking',
                '24/7 client portal access',
                'Lifetime credit guidance + funding readiness',
            ],
        ],

        // ───────────────────────────── RED ───────────────────────────────
        'vip' => [
            'key'          => 'vip',
            'label'        => 'VIP Plan',
            'tag'          => 'VIP PLAN',
            'tagline'      => 'White-Glove · Done-With-You',
            'desc'         => 'Our most exclusive, done-with-you service. A dedicated senior strategist, front-of-line priority on everything, a private text line, and weekly 1-on-1 strategy calls.',
            'amount'       => '1997.00',
            'recurring'    => null,
            'compare_at'   => null,
            'save'         => null,
            'price_big'    => '1,997',
            'period'       => 'white-glove program',
            'billing_note' => 'white-glove coaching service',
            'sub_note'     => 'White-glove service',
            'color'        => 'cr',
            'btn'          => 'btn-r',
            'tag_class'    => 'navy-tag',
            'badge'        => '★ VIP',
            'is_couples'   => false,
            'cta'          => 'Apply For VIP',
            'features'     => [
                'Done-with-you white-glove priority service — we do the work for you',
                'Dedicated senior credit strategist assigned to your case',
                'Private direct text line to your specialist — replies in minutes',
                'Front-of-line priority disputes across all 3 bureaus — fastest turnaround',
                'Public records, collections & hard inquiries aggressively challenged',
                'Weekly 1-on-1 progress & strategy calls',
                '24/7 VIP client portal + real-time score tracking',
                'Lender & funding introductions + lifetime VIP guidance',
            ],
        ],

    ],
];
