<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadController extends Controller
{
    public function submit(Request $request): RedirectResponse
    {
        // ── HONEYPOT BOT DETECTION ───────────────────────────────────────────
        $honeypotWebsite = $request->input('user_website');
        $honeypotBusiness = $request->input('business_name');
        
        if (!empty($honeypotWebsite) || !empty($honeypotBusiness)) {
            Log::warning('🤖 BOT DETECTED - LeadController honeypot triggered', [
                'ip'               => $request->ip(),
                'user_agent'       => $request->userAgent(),
                'honeypot_website' => $honeypotWebsite,
                'honeypot_business'=> $honeypotBusiness,
                'email'            => $request->input('email'),
                'phone'            => $request->input('phone'),
                'timestamp'        => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'Fuck off');
        }

        // ── RECAPTCHA VERIFICATION ───────────────────────────────────────────
        $recaptchaToken = $request->input('g-recaptcha-response');

        if (empty($recaptchaToken)) {
            return back()->withInput()->withErrors(['recaptcha' => 'Please complete the reCAPTCHA verification.']);
        }

        $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => env('RECAPTCHA_SECRET_KEY'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip(),
        ]);

        if (!$recaptchaResponse->json('success')) {
            Log::warning('🤖 reCAPTCHA verification failed - LeadController', [
                'ip'            => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'email'         => $request->input('email'),
                'error_codes'   => $recaptchaResponse->json('error-codes'),
                'timestamp'     => now()->toDateTimeString(),
            ]);

            return back()->withInput()->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        // ── BOT PHONE PATTERN DETECTION ──────────────────────────────────────
        $phone = $request->input('phone', '');
        $phoneDigits = preg_replace('/\D/', '', $phone);
        
        if ($this->isBotPhone($phoneDigits, $phone)) {
            Log::warning('🤖 BOT DETECTED - LeadController bot phone pattern', [
                'ip'               => $request->ip(),
                'user_agent'       => $request->userAgent(),
                'phone_raw'        => $phone,
                'phone_digits'     => $phoneDigits,
                'email'            => $request->input('email'),
                'timestamp'        => now()->toDateTimeString(),
                'detection_reason' => $this->getBotPhoneReason($phoneDigits, $phone),
            ]);

            return back()->with('success', 'Thank you. Your request has been submitted.');
        }

        $validated = $request->validate([
            'first_name'        => ['required', 'string', 'max:100'],
            'last_name'         => ['required', 'string', 'max:100'],
            'email'             => ['required', 'email', 'max:150'],
            'phone'             => ['required', 'string', 'max:30'],
            'credit_score_range'=> ['nullable', 'string', 'max:255'],
            'primary_goal'      => ['nullable', 'string', 'max:255'],
            'message'           => ['nullable', 'string', 'max:2000'],
            'sms_consent'       => ['required', 'accepted'],
            'user_website'      => ['nullable', 'max:10'],
            'business_name'     => ['nullable', 'max:10'],
            'lead_source'       => ['nullable', 'string', 'max:255'],
            'page_url'          => ['nullable', 'string', 'max:500'],
            'form_name'         => ['nullable', 'string', 'max:255'],
        ]);

        if (!config('disputefox.enabled')) {
            return back()->withInput()->with('error', 'Lead system is currently disabled.');
        }

        $payload = [
            'method'                  => config('disputefox.method'),
            'tab_info_id'             => config('disputefox.tab_info_id'),
            'redirect_url'            => config('disputefox.redirect_url'),
            'company_id'              => config('disputefox.company_id'),
            'cust_type'               => config('disputefox.cust_type'),
            'add_affiliate_flag'      => config('disputefox.add_affiliate_flag'),
            'assignedto_id'           => config('disputefox.assignedto_id'),
            'sales_representative_id' => config('disputefox.sales_representative_id'),
            'workflow_statusid'       => config('disputefox.workflow_statusid'),
            'folder_statusid'         => config('disputefox.folder_statusid'),
            'customer_statusid'       => config('disputefox.customer_statusid'),
            'portalAccess'            => config('disputefox.portalAccess'),
            'customerAgreementIDs'    => config('disputefox.customerAgreementIDs'),
            'firstName'               => $validated['first_name'],
            'lastName'                => $validated['last_name'],
            'email'                   => $validated['email'],
            'mobilePhone'             => $validated['phone'],
            'textArea1'               => $this->buildCommentBlock($validated, $request),
            'checkbox1'               => 'true',
        ];

        try {
            $response = Http::asForm()
                ->timeout(30)
                ->withHeaders(['Accept' => '*/*', 'User-Agent' => 'Laravel Lead Integration'])
                ->post(config('disputefox.url') . '?method=' . urlencode((string) config('disputefox.method')), $payload);

            $body = (string) $response->body();

            if (!$response->successful()) {
                return back()->withInput()->with('error', 'Unable to submit your request right now. Please try again.');
            }

            if (stripos($body, 'Account is in-activated') !== false) {
                return back()->withInput()->with('error', 'The lead account is currently inactive. Please contact support.');
            }

            $this->sendToGhl($validated, $request);

            return back()->with('success', 'Your request was submitted successfully. We will contact you shortly.');
        } catch (\Throwable $e) {
            Log::error('DisputeFox submission failed', ['message' => $e->getMessage(), 'email' => $validated['email'] ?? null]);
            return back()->withInput()->with('error', 'Submission failed. Please try again in a moment.');
        }
    }

    /**
     * Detect bot phone patterns
     */
    private function isBotPhone(string $digits, string $raw): bool
    {
        // Pattern 1: Starts with 8, exactly 11 digits, no formatting
        if (strlen($digits) === 11 && str_starts_with($digits, '8') && !preg_match('/[\(\)\-\s]/', $raw)) {
            return true;
        }

        // Pattern 2: Exactly 10 digits, no formatting at all
        if (strlen($digits) === 10 && !preg_match('/[\+\(\)\-\s]/', $raw)) {
            return true;
        }

        // Pattern 3: All same digit repeated
        if (strlen($digits) >= 10 && count(array_unique(str_split($digits))) === 1) {
            return true;
        }

        // Pattern 4: Sequential digits
        if (strlen($digits) >= 10 && $this->isSequential($digits)) {
            return true;
        }

        return false;
    }

    /**
     * Get reason for bot phone detection
     */
    private function getBotPhoneReason(string $digits, string $raw): string
    {
        if (strlen($digits) === 11 && str_starts_with($digits, '8') && !preg_match('/[\(\)\-\s]/', $raw)) {
            return '11 digits starting with 8, no formatting';
        }
        if (strlen($digits) === 10 && !preg_match('/[\+\(\)\-\s]/', $raw)) {
            return '10 digits with zero formatting';
        }
        if (count(array_unique(str_split($digits))) === 1) {
            return 'All same digit repeated';
        }
        if ($this->isSequential($digits)) {
            return 'Sequential digits detected';
        }
        return 'Unknown bot pattern';
    }

    /**
     * Check if digits are sequential
     */
    private function isSequential(string $digits): bool
    {
        for ($i = 0; $i < strlen($digits) - 1; $i++) {
            if ((int)$digits[$i] + 1 !== (int)$digits[$i + 1]) {
                return false;
            }
        }
        return true;
    }

    private function buildCommentBlock(array $validated, Request $request): string
    {
        $lines = [];
        if (!empty($validated['credit_score_range'])) $lines[] = 'Current Credit Score Range: ' . $validated['credit_score_range'];
        if (!empty($validated['primary_goal'])) $lines[] = 'Primary Goal: ' . $validated['primary_goal'];
        if (!empty($validated['message'])) $lines[] = 'Message: ' . $validated['message'];
        if (!empty($validated['lead_source'])) $lines[] = 'Lead Source: ' . $validated['lead_source'];
        if (!empty($validated['form_name'])) $lines[] = 'Form Name: ' . $validated['form_name'];
        if (!empty($validated['page_url'])) $lines[] = 'Page URL: ' . $validated['page_url'];
        $lines[] = 'SMS Consent: Yes';
        $lines[] = 'IP Address: ' . $request->ip();
        $lines[] = 'Submitted At: ' . now()->toDateTimeString();
        return implode("\n", $lines);
    }

    private function sendToGhl(array $validated, Request $request): void
    {
        $ghlWebhookUrl = config('services.ghl.lead_webhook_url');
        if (empty($ghlWebhookUrl)) {
            Log::warning('GHL lead webhook skipped — GHL_LEAD_URL not set');
            return;
        }

        $ghlPayload = [
            'first_name'         => $validated['first_name'],
            'last_name'          => $validated['last_name'],
            'full_name'          => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email'              => $validated['email'],
            'phone'              => $validated['phone'],
            'credit_score_range' => $validated['credit_score_range'] ?? '',
            'primary_goal'       => $validated['primary_goal'] ?? '',
            'message'            => $validated['message'] ?? '',
            'sms_consent'        => 'Yes',
            'lead_source'        => $validated['lead_source'] ?? '',
            'page_url'           => $validated['page_url'] ?? '',
            'form_name'          => $validated['form_name'] ?? '',
            'ip_address'         => $request->ip(),
            'submitted_at'       => now()->toDateTimeString(),
            'source'             => 'Website Form',
        ];

        try {
            $ghlResponse = Http::asJson()->timeout(20)->post($ghlWebhookUrl, $ghlPayload);
            Log::info('GHL lead webhook fired', [
                'status'  => $ghlResponse->status(),
                'success' => $ghlResponse->successful(),
                'email'   => $validated['email'],
            ]);
        } catch (\Throwable $e) {
            Log::error('GHL lead webhook failed', ['message' => $e->getMessage(), 'email' => $validated['email'] ?? null]);
        }
    }
}