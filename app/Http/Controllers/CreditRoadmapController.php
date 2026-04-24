<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class CreditRoadmapController extends Controller
{
    // ─── Security: configurable thresholds ───────────────────────────────────

    /** Minimum seconds between form render and submission (time-trap) */
    private const TIME_TRAP_MIN_SECONDS = 3;

    /** Max submissions per IP per window */
    private const RATE_LIMIT_MAX_ATTEMPTS = 5;

    /** Rate-limit window in seconds (10 minutes) */
    private const RATE_LIMIT_DECAY_SECONDS = 600;

    // ─────────────────────────────────────────────────────────────────────────

    public function submit(Request $request): JsonResponse
    {
        Log::info('CreditRoadmap submit() called', [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
            'page_url'   => $request->input('page_url'),
            'page_title' => $request->input('page_title'),
            'email'      => $request->input('email'),
            'phone'      => $request->input('phone'),
        ]);

        try {

            // ── 1. RATE LIMIT ─────────────────────────────────────────────────
            $rateLimitKey = 'roadmap_submit:' . $request->ip();

            if (RateLimiter::tooManyAttempts($rateLimitKey, self::RATE_LIMIT_MAX_ATTEMPTS)) {
                $seconds = RateLimiter::availableIn($rateLimitKey);

                Log::warning('CreditRoadmap rate limit hit', [
                    'ip'             => $request->ip(),
                    'retry_after_s'  => $seconds,
                ]);

                return response()->json([
                    'message' => 'Too many submissions. Please wait a moment and try again.',
                ], 429);
            }

            RateLimiter::hit($rateLimitKey, self::RATE_LIMIT_DECAY_SECONDS);

            // ── 2. HONEYPOT (DUAL FIELD) ─────────────────────────────────────
            $honeypotWebsite = $request->input('user_website');
            $honeypotBusiness = $request->input('business_name');

            if (!empty($honeypotWebsite) || !empty($honeypotBusiness)) {
                Log::warning('🤖 BOT DETECTED - CreditRoadmap honeypot triggered', [
                    'ip'               => $request->ip(),
                    'user_agent'       => $request->userAgent(),
                    'honeypot_website' => $honeypotWebsite,
                    'honeypot_business'=> $honeypotBusiness,
                    'email'            => $request->input('email'),
                    'phone'            => $request->input('phone'),
                    'timestamp'        => now()->toDateTimeString(),
                    'referer'          => $request->header('referer'),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Your request was submitted successfully. We will contact you shortly.',
                ]);
            }

            // ── 2.5. BOT PHONE PATTERN DETECTION ─────────────────────────────
            $phone = $request->input('phone', '');
            $phoneDigits = preg_replace('/\D/', '', $phone);

            // Check for bot phone patterns
            if ($this->isBotPhone($phoneDigits, $phone)) {
                Log::warning('🤖 BOT DETECTED - CreditRoadmap bot phone pattern', [
                    'ip'               => $request->ip(),
                    'user_agent'       => $request->userAgent(),
                    'phone_raw'        => $phone,
                    'phone_digits'     => $phoneDigits,
                    'email'            => $request->input('email'),
                    'timestamp'        => now()->toDateTimeString(),
                    'detection_reason' => $this->getBotPhoneReason($phoneDigits, $phone),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Your request was submitted successfully. We will contact you shortly.',
                ]);
            }

            // ── 3. TIME-TRAP ──────────────────────────────────────────────────
            $renderedAt = (int) $request->input('form_rendered_at', 0);

            if ($renderedAt > 0) {
                $elapsed = (time() * 1000 - $renderedAt) / 1000;

                if ($elapsed < self::TIME_TRAP_MIN_SECONDS) {
                    Log::warning('CreditRoadmap time-trap triggered', [
                        'ip'         => $request->ip(),
                        'elapsed_s'  => $elapsed,
                        'threshold'  => self::TIME_TRAP_MIN_SECONDS,
                    ]);

                    return response()->json([
                        'message' => 'Submission rejected. Please fill out the form normally.',
                    ], 422);
                }
            }

            // ── 4. hCAPTCHA ───────────────────────────────────────────────────
            $hCaptchaToken   = $request->input('h-captcha-response', '');
            $hCaptchaSecret  = config('services.hcaptcha.secret');

            if (!empty($hCaptchaSecret)) {
                if (empty($hCaptchaToken)) {
                    Log::warning('CreditRoadmap hCaptcha token missing', [
                        'ip' => $request->ip(),
                    ]);

                    return response()->json([
                        'message' => 'Please complete the captcha verification.',
                    ], 422);
                }

                try {
                    $captchaResponse = Http::asForm()
                        ->timeout(10)
                        ->post('https://hcaptcha.com/siteverify', [
                            'secret'   => $hCaptchaSecret,
                            'response' => $hCaptchaToken,
                            'remoteip' => $request->ip(),
                        ]);

                    $captchaData = $captchaResponse->json();

                    Log::info('CreditRoadmap hCaptcha verification result', [
                        'ip'      => $request->ip(),
                        'success' => $captchaData['success'] ?? false,
                    ]);

                    if (empty($captchaData['success'])) {
                        return response()->json([
                            'message' => 'Captcha verification failed. Please try again.',
                        ], 422);
                    }
                } catch (Throwable $e) {
                    Log::error('CreditRoadmap hCaptcha request exception', [
                        'message' => $e->getMessage(),
                        'ip'      => $request->ip(),
                    ]);

                    if (!config('services.hcaptcha.fail_open', false)) {
                        return response()->json([
                            'message' => 'Unable to verify captcha. Please try again.',
                        ], 422);
                    }
                }
            } else {
                Log::warning('CreditRoadmap hCaptcha secret not configured — skipping verification');
            }

            // ── 4.5. GOOGLE reCAPTCHA ─────────────────────────────────────────
            $recaptchaToken  = $request->input('g-recaptcha-response', '');
            $recaptchaSecret = env('RECAPTCHA_SECRET_KEY');

            if (!empty($recaptchaSecret)) {
                if (empty($recaptchaToken)) {
                    Log::warning('CreditRoadmap reCAPTCHA token missing', [
                        'ip' => $request->ip(),
                    ]);

                    return response()->json([
                        'message' => 'Please complete the reCAPTCHA verification.',
                    ], 422);
                }

                try {
                    $recaptchaResponse = Http::asForm()
                        ->timeout(10)
                        ->post('https://www.google.com/recaptcha/api/siteverify', [
                            'secret'   => $recaptchaSecret,
                            'response' => $recaptchaToken,
                            'remoteip' => $request->ip(),
                        ]);

                    $recaptchaData = $recaptchaResponse->json();

                    Log::info('CreditRoadmap reCAPTCHA verification result', [
                        'ip'      => $request->ip(),
                        'success' => $recaptchaData['success'] ?? false,
                    ]);

                    if (!($recaptchaData['success'] ?? false)) {
                        return response()->json([
                            'message' => 'Please complete the reCAPTCHA.',
                        ], 422);
                    }
                } catch (Throwable $e) {
                    Log::error('CreditRoadmap reCAPTCHA request exception', [
                        'message' => $e->getMessage(),
                        'ip'      => $request->ip(),
                    ]);

                    return response()->json([
                        'message' => 'Unable to verify reCAPTCHA. Please try again.',
                    ], 422);
                }
            } else {
                Log::warning('CreditRoadmap reCAPTCHA secret not configured — skipping verification');
            }

            // ── 5. VALIDATION ─────────────────────────────────────────────────
            Log::info('CreditRoadmap validation started');

            $validated = $request->validate([
                'first_name'  => ['required', 'string', 'max:100'],
                'last_name'   => ['required', 'string', 'max:100'],
                'email'       => ['required', 'email:rfc,dns', 'max:150'],
                'phone'       => ['required', 'string', 'regex:/^[\+\(\)\-\s0-9]{7,30}$/', 'max:30'],

                'score'       => ['required', 'string', 'max:255'],
                'items'       => ['required', 'string', 'max:255'],
                'goal'        => ['required', 'string', 'max:255'],
                'timeline'    => ['required', 'string', 'max:255'],

                'full_name'          => ['nullable', 'string', 'max:255'],
                'form_source'        => ['nullable', 'string', 'max:255'],
                'page_url'           => ['nullable', 'string', 'max:500'],
                'page_title'         => ['nullable', 'string', 'max:255'],

                'user_website'       => ['nullable', 'string'],
                'business_name'      => ['nullable', 'string'],
                'form_rendered_at'   => ['nullable', 'integer'],
                'h-captcha-response' => ['nullable', 'string'],
                'g-recaptcha-response' => ['nullable', 'string'],
            ]);

            Log::info('CreditRoadmap validation passed', [
                'email'       => $validated['email'] ?? null,
                'phone'       => $validated['phone'] ?? null,
                'first_name'  => $validated['first_name'] ?? null,
                'last_name'   => $validated['last_name'] ?? null,
            ]);

            if (!config('disputefox.enabled')) {
                Log::warning('CreditRoadmap aborted: DisputeFox integration disabled');
                return response()->json(['message' => 'Lead system is currently disabled.'], 500);
            }

            $commentBlock = $this->buildCommentBlock($validated, $request);

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
                'textArea1'               => $commentBlock,
                'checkbox1'               => 'true',
            ];

            try {
                $endpoint = config('disputefox.url') . '?method=' . urlencode((string) config('disputefox.method'));

                $response = Http::asForm()
                    ->timeout(30)
                    ->withHeaders(['Accept' => '*/*', 'User-Agent' => 'Laravel Credit Roadmap Integration'])
                    ->post($endpoint, $payload);

                $body = (string) $response->body();

                if (!$response->successful()) {
                    Log::error('CreditRoadmap DisputeFox request failed', [
                        'status' => $response->status(),
                        'email'  => $validated['email'],
                    ]);
                    return response()->json(['message' => 'Unable to submit your request right now. Please try again.'], 500);
                }

                if (stripos($body, 'Account is in-activated') !== false) {
                    return response()->json(['message' => 'The lead account is currently inactive. Please contact support.'], 500);
                }

                $this->sendToGhl($validated, $request);

                return response()->json([
                    'success' => true,
                    'message' => 'Your request was submitted successfully. We will contact you shortly.',
                ]);
            } catch (Throwable $e) {
                Log::error('CreditRoadmap DisputeFox submission exception', [
                    'message' => $e->getMessage(),
                    'email'   => $validated['email'] ?? null,
                ]);
                return response()->json(['message' => 'Submission failed. Please try again in a moment.'], 500);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('CreditRoadmap top-level exception', [
                'message' => $e->getMessage(),
                'email'   => $request->input('email'),
            ]);
            return response()->json(['message' => 'Submission failed. Please try again in a moment.'], 500);
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

        // Pattern 2: Exactly 10 digits, no formatting at all (like: 2489043176)
        if (strlen($digits) === 10 && !preg_match('/[\+\(\)\-\s]/', $raw)) {
            return true;
        }

        // Pattern 3: All same digit repeated (like: 1111111111)
        if (strlen($digits) >= 10 && count(array_unique(str_split($digits))) === 1) {
            return true;
        }

        // Pattern 4: Sequential digits (like: 1234567890)
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
        if (!empty($validated['score'])) $lines[] = 'Current Credit Score Range: ' . $validated['score'];
        if (!empty($validated['items'])) $lines[] = 'Negative Items: ' . $validated['items'];
        if (!empty($validated['goal'])) $lines[] = 'Primary Goal: ' . $validated['goal'];
        if (!empty($validated['timeline'])) $lines[] = 'Desired Timeline: ' . $validated['timeline'];
        if (!empty($validated['form_source'])) $lines[] = 'Lead Source: ' . $validated['form_source'];
        if (!empty($validated['page_title'])) $lines[] = 'Page Title: ' . $validated['page_title'];
        if (!empty($validated['page_url'])) $lines[] = 'Page URL: ' . $validated['page_url'];
        $lines[] = 'IP Address: ' . $request->ip();
        $lines[] = 'Submitted At: ' . now()->toDateTimeString();
        return implode("\n", $lines);
    }

    private function sendToGhl(array $validated, Request $request): void
    {
        $ghlWebhookUrl = config('services.ghl.roadmap_webhook_url');
        if (empty($ghlWebhookUrl)) {
            Log::warning('Roadmap GHL webhook skipped — GHL_ROADMAP_WEBHOOK_URL not set');
            return;
        }

        $ghlPayload = [
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'full_name'    => !empty($validated['full_name']) ? $validated['full_name'] : trim($validated['first_name'] . ' ' . $validated['last_name']),
            'email'        => $validated['email'],
            'phone'        => $validated['phone'],
            'score'        => $validated['score'] ?? '',
            'items'        => $validated['items'] ?? '',
            'goal'         => $validated['goal'] ?? '',
            'timeline'     => $validated['timeline'] ?? '',
            'form_source'  => $validated['form_source'] ?? '850 FICO CLUB Roadmap Popup',
            'page_url'     => $validated['page_url'] ?? '',
            'page_title'   => $validated['page_title'] ?? '',
            'ip_address'   => $request->ip(),
            'submitted_at' => now()->toDateTimeString(),
            'source'       => 'Credit Roadmap Popup',
        ];

        try {
            $ghlResponse = Http::asJson()->timeout(20)->post($ghlWebhookUrl, $ghlPayload);
            Log::info('Roadmap GHL webhook response received', [
                'status'  => $ghlResponse->status(),
                'success' => $ghlResponse->successful(),
                'email'   => $validated['email'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('Roadmap GHL webhook failed', [
                'message' => $e->getMessage(),
                'email'   => $validated['email'] ?? null,
            ]);
        }
    }
}