<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Basic Pages
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'referral'])->get('/', function () {
    return view('index');
});

// Retired Oct 2026: checkout + enrollment now run on GHL (packages.850ficoclub.com),
// and the admin dashboard / Commas integration was removed.
// Old links and bookmarks land on the site instead of a 404.
Route::redirect('/accept-checkout',    '/#pricing');
Route::redirect('/onboardingform',     '/');
Route::redirect('/couples-onboarding', '/');
Route::redirect('/admin',              '/');
Route::redirect('/admin/{any}',        '/')->where('any', '.*');

Route::get('/funding', function () {
    return view('funding');
})->name('funding');

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

// Free Consultation form + Credit Roadmap popup removed Oct 2026 (visitors now pay or book a call).
// /lead-submit and /credit-roadmap/submit no longer exist.

/*
|--------------------------------------------------------------------------
| Funding forms
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\FundingFormController;
use App\Http\Controllers\FundingReadyFormController;

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
