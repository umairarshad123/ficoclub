<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnboardingFormController;
use App\Http\Controllers\CouplesController;


/*
|--------------------------------------------------------------------------
| Basic Pages
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'referral'])->get('/', function () {
    return view('index');
});

Route::get('/onboardingform', [OnboardingFormController::class, 'show'])
    ->name('onboarding.form');

/*
|--------------------------------------------------------------------------
| Couples Glow-Up — two-person onboarding flow
|   Hub → Husband → Hub → Wife → Done
|--------------------------------------------------------------------------
*/
Route::get('/couples-onboarding', [CouplesController::class, 'hub'])
    ->name('couples.hub');

Route::post('/couples-onboarding/complete', [CouplesController::class, 'complete'])
    ->name('couples.complete');
    
Route::get('/funding', function () {
    return view('funding');
});
Route::get('/fundingg', function () {
    return view('fundingall');
});
Route::view('/consumer-credit-file-rights', 'legal.consumer-credit-file-rights')
    ->name('consumer-credit-file-rights');

Route::view('/notice-of-cancellation', 'legal.notice-of-cancellation')
    ->name('notice-of-cancellation');

Route::view('/service-agreement', 'legal.service-agreement')
    ->name('service-agreement');

Route::view('/privacy-policy', 'legal.privacy-policy')
    ->name('privacy-policy');

Route::view('/terms-of-service', 'legal.terms-of-service')
    ->name('terms-of-service');
    
/*
|--------------------------------------------------------------------------
| Lead Form Submission
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\LeadController;

Route::post('/lead-submit', [LeadController::class, 'submit'])
    ->name('lead.submit');


/*
|--------------------------------------------------------------------------
| Credit Roadmap Form Submission
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CreditRoadmapController;

Route::post('/credit-roadmap/submit', [CreditRoadmapController::class, 'submit'])
    ->name('credit.roadmap.submit');




/*
|--------------------------------------------------------------------------
| New Accept.JS Flow (custom branded checkout, direct charge)
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AcceptJsPaymentController;
use App\Http\Controllers\CommasCheckoutController;
use App\Http\Controllers\CommasWebhookController;

Route::middleware(['web', 'referral'])->group(function () {

    // PAYMENT_PROVIDER (config/payments.php) picks which checkout this URL renders.
    Route::get('/accept-checkout', config('payments.provider') === 'commas'
            ? [CommasCheckoutController::class, 'show']
            : [AcceptJsPaymentController::class, 'showCheckout'])
        ->name('accept.checkout');

    // Legacy raw-card charge — refuses to run once Commas is the active provider.
    Route::post('/accept-payment', [AcceptJsPaymentController::class, 'processPayment'])
        ->name('accept.payment');

    // Commas embedded checkout
    Route::post('/checkout/order', [CommasCheckoutController::class, 'createOrder'])
        ->middleware('throttle:10,1')
        ->name('checkout.order');
    Route::post('/checkout/confirm', [CommasCheckoutController::class, 'confirm'])
        ->middleware('throttle:30,1')
        ->name('checkout.confirm');
    Route::get('/checkout/status/{uuid}', [CommasCheckoutController::class, 'status'])
        ->middleware('throttle:60,1')
        ->whereUuid('uuid')
        ->name('checkout.status');
    Route::get('/checkout/complete/{uuid}', [CommasCheckoutController::class, 'complete'])
        ->whereUuid('uuid')
        ->name('checkout.complete');

});

Route::post('/webhooks/commas', [CommasWebhookController::class, 'handle'])
    ->name('webhooks.commas');

// ─────────────────────────────────────────────────────────────────────────────
// ADD THESE ROUTES TO YOUR routes/web.php
// ─────────────────────────────────────────────────────────────────────────────
 
use App\Http\Controllers\FundingFormController;
use App\Http\Controllers\FundingReadyFormController;
 
// ── /funding  — Full page with hero, content, new typeform, footer ────────────
Route::get('/funding', function () {
    return view('funding');   // resources/views/funding.blade.php
})->name('funding');
 
// ── /funding-ready-form  — Form only page ────────────────────────────────────
Route::get('/funding-ready-form', function () {
    return view('funding-ready-form');   // resources/views/funding-ready-form.blade.php
})->name('funding.ready.form');
 
// ── POST: New typeform submission → Google Sheets ─────────────────────────────
// Used by BOTH pages (funding page and funding-ready-form page)
Route::post('/funding-ready/submit', [FundingReadyFormController::class, 'submit'])
    ->name('funding.ready.submit');
 
// ── POST: Original full funding form (existing — keep as-is) ─────────────────
Route::post('/funding/submit', [FundingFormController::class, 'submit'])
    ->name('funding.submit');
 
// ── GET: Secure file download (existing — keep as-is) ────────────────────────
Route::get('/funding/download', [FundingFormController::class, 'download'])
    ->name('funding.download');
    
    

use App\Http\Controllers\AuthorizeNetWebhookController;

Route::post('/webhooks/authorize-net', [AuthorizeNetWebhookController::class, 'handle'])
    ->name('webhooks.authorize-net');


// ── Apple Pay domain verification (Commas embedded checkout) ───────────────
// Served through Laravel because the cPanel deploy (`cp -R *`) skips dot-folders,
// so a static .well-known/ file in the repo would never reach public_html.
// Apple requires a 200 at this exact path — no redirect, no extension.
Route::get('/.well-known/apple-developer-merchantid-domain-association', function () {
    return response()->file(
        resource_path('apple-pay/apple-developer-merchantid-domain-association'),
        ['Content-Type' => 'text/plain']
    );
});
    

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

Route::prefix('admin')->group(function () {

    // Public login endpoints
    Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('admin.login.show');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');

    // Gated admin routes
Route::middleware('admin.auth')->group(function () {

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Subscriptions
        Route::get('/subscriptions',          [DashboardController::class, 'subscriptionsIndex'])->name('admin.subscriptions');
        Route::get('/subscriptions/export',   [DashboardController::class, 'exportCsv'])->name('admin.subscriptions.csv');
        Route::get('/subscriptions/{id}',     [DashboardController::class, 'subscriptionShow'])->name('admin.subscription.show')->whereNumber('id');

        // Payments (all-payments listing — initial / recurring / refund / void)
        Route::get('/payments',         [DashboardController::class, 'paymentsIndex'])->name('admin.payments');
        Route::get('/payments/export',  [DashboardController::class, 'paymentsExportCsv'])->name('admin.payments.csv');

        // Commas sales — every Commas transaction (website + funnels / payment links)
        Route::get('/commas-sales',         [DashboardController::class, 'commasSales'])->name('admin.commas-sales');
        Route::get('/commas-sales/export',  [DashboardController::class, 'commasSalesCsv'])->name('admin.commas-sales.csv');
        Route::post('/commas/sync',         [DashboardController::class, 'commasSync'])->name('admin.commas.sync');

        // Checkout orders (Commas) — every checkout attempt, paid or not
        Route::get('/orders', [DashboardController::class, 'ordersIndex'])->name('admin.orders');

        // Leads (NEW)
        Route::get('/leads',          [DashboardController::class, 'leadsIndex'])->name('admin.leads');
        Route::get('/leads/export',   [DashboardController::class, 'leadsExportCsv'])->name('admin.leads.csv');
        Route::get('/leads/{id}',     [DashboardController::class, 'leadShow'])->name('admin.lead.show')->whereNumber('id');

        // At-Risk (NEW)
        Route::get('/at-risk', [DashboardController::class, 'atRisk'])->name('admin.at-risk');

        // Referrals
        Route::get('/referrals', [DashboardController::class, 'referrals'])->name('admin.referrals');

        // Webhooks (Authorize.Net real-time monitoring)
        Route::get('/webhooks',       [DashboardController::class, 'webhooksIndex'])->name('admin.webhooks');
        Route::get('/webhooks/{id}',  [DashboardController::class, 'webhookShow'])->name('admin.webhook.show')->whereNumber('id');
    });

});



