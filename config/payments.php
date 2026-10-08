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

];
