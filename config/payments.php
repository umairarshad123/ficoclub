<?php

/*
|--------------------------------------------------------------------------
| Active payment provider
|--------------------------------------------------------------------------
|
| authorize_net — legacy raw-card checkout (AcceptJsPaymentController)
| commas        — Commas embedded checkout (CommasCheckoutController)
|
| /accept-checkout renders whichever is active. Flip with PAYMENT_PROVIDER
| in .env. The Authorize.Net webhook stays live either way so late
| refunds / chargebacks on old transactions are still recorded.
|
*/

return [

    'provider' => env('PAYMENT_PROVIDER', 'authorize_net'),

    // Card-processing surcharge Commas adds on top of every price (observed: exactly 4%).
    // Shown to customers on the pricing cards + checkout. Editable on Admin → Plans & Pricing.
    'surcharge_percent' => (float) env('COMMAS_SURCHARGE_PERCENT', 4),

];
