<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AcceptJsPaymentController extends Controller
{
    public function showCheckout()
    {
        Log::info('Accept.js checkout page opened', [
            'ip'            => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'url'           => request()->fullUrl(),
            'method'        => request()->method(),
            'session_id'    => session()->getId(),
            'referral_code' => session('referral_code', null),
        ]);

        return view('payments.accept-checkout');
    }

    public function processPayment(Request $request)
    {
        Log::info('Accept.js payment request started', [
            'ip'                     => $request->ip(),
            'user_agent'             => $request->userAgent(),
            'url'                    => $request->fullUrl(),
            'method'                 => $request->method(),
            'session_id'             => session()->getId(),
            'request_has_descriptor' => $request->filled('dataDescriptor'),
            'request_has_data_value' => $request->filled('dataValue'),
            'selected_plan_raw'      => $request->input('selected_plan'),
            'referral_code'          => $request->input('referral_code') ?: session('referral_code', null),
        ]);

        Log::info('Accept.js payment request raw input snapshot', [
            'first_name'       => $request->input('first_name'),
            'last_name'        => $request->input('last_name'),
            'email'            => $request->input('email'),
            'phone'            => $request->input('phone'),
            'city'             => $request->input('city'),
            'state'            => $request->input('state'),
            'zip'              => $request->input('zip'),
            'cardName'         => $request->input('cardName'),
            'selected_plan'    => $request->input('selected_plan'),
            'agree_terms'      => $request->input('agree_terms'),
            'agree_privacy'    => $request->input('agree_privacy'),
            'marketing_opt_in' => $request->input('marketing_opt_in'),
            'referral_code'    => $request->input('referral_code'),
            'dataDescriptor'   => $request->input('dataDescriptor'),
            'dataValue_length' => strlen((string) $request->input('dataValue')),
        ]);

        $validated = $request->validate([
            'dataDescriptor'   => 'required|string',
            'dataValue'        => 'required|string',
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'required|string|max:30',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:10',
            'zip'              => 'required|string|max:20',
            'cardName'         => 'required|string|max:150',
            'selected_plan'    => 'nullable|string|in:silver,gold,platinum',
            'agree_terms'      => 'required|boolean',
            'agree_privacy'    => 'required|boolean',
            'marketing_opt_in' => 'nullable|boolean',
            'referral_code'    => 'nullable|string|max:50',
        ]);

        Log::info('Accept.js validation completed successfully', [
            'email'            => $validated['email'],
            'selected_plan'    => $validated['selected_plan'] ?? null,
            'referral_code'    => $validated['referral_code'] ?? null,
            'agree_terms'      => $validated['agree_terms'],
            'agree_privacy'    => $validated['agree_privacy'],
            'marketing_opt_in' => $validated['marketing_opt_in'] ?? null,
        ]);

        // Resolve referral code: form input → session → cookie (priority order)
        $referralCode = strtoupper(trim(
            $validated['referral_code']
            ?? session('referral_code', '')
            ?? ''
        )) ?: null;

        Log::info('Referral code resolved', [
            'invoice_pending'         => true,
            'validated_referral_code' => $validated['referral_code'] ?? null,
            'session_referral_code'   => session('referral_code', null),
            'resolved_referral_code'  => $referralCode,
        ]);

        Log::info('Accept.js payment request validated', [
            'email'            => $validated['email'],
            'name'             => $validated['first_name'] . ' ' . $validated['last_name'],
            'phone'            => $validated['phone'],
            'city'             => $validated['city'],
            'state'            => $validated['state'],
            'zip'              => $validated['zip'],
            'cardName'         => $validated['cardName'],
            'agree_terms'      => $validated['agree_terms'],
            'agree_privacy'    => $validated['agree_privacy'],
            'marketing_opt_in' => $validated['marketing_opt_in'] ?? 0,
            'dataDescriptor'   => $validated['dataDescriptor'],
            'dataValue_length' => strlen($validated['dataValue']),
            'referral_code'    => $referralCode,
        ]);

                $planMap = [
                    'silver' => [
                        'amount'    => '299.00',
                        'label'     => 'Silver Membership',
                        'recurring' => '149.00',
                    ],
                    'gold' => [
                        'amount'    => '399.00',
                        'label'     => 'Gold Membership',
                        'recurring' => '199.00',
                    ],
                    'platinum' => [
                        'amount'    => '499.00',
                        'label'     => 'Platinum Membership',
                        'recurring' => '249.00',
                    ],
                ];
                
                $planKey      = isset($planMap[$validated['selected_plan'] ?? '']) ? $validated['selected_plan'] : 'platinum';
                $amount       = $planMap[$planKey]['amount'];
                $planLabel    = $planMap[$planKey]['label'];
                $recurringAmt = $planMap[$planKey]['recurring'];

        Log::info('Plan resolved from request', [
            'selected_plan_input' => $validated['selected_plan'] ?? null,
            'resolved_plan_key'   => $planKey,
            'plan_label'          => $planLabel,
            'amount'              => $amount,
            'recurring_amount'    => $recurringAmt,
        ]);

        $invoiceNumber = 'INV-' . time();

        Log::info('Accept.js payment invoice generated', [
            'invoice'       => $invoiceNumber,
            'plan_key'      => $planKey,
            'plan_label'    => $planLabel,
            'amount'        => $amount,
            'recurring_amt' => $recurringAmt ?? 'none',
            'referral_code' => $referralCode,
        ]);

        $environment = config('services.authorize_net.environment');
        $apiLoginId  = config('services.authorize_net.api_login_id');
        $txKey       = config('services.authorize_net.transaction_key');

        $endpoint = $environment === 'production'
            ? 'https://api.authorize.net/xml/v1/request.api'
            : 'https://apitest.authorize.net/xml/v1/request.api';

        Log::info('Authorize.Net environment resolved', [
            'invoice'       => $invoiceNumber,
            'environment'   => $environment,
            'endpoint'      => $endpoint,
            'api_login_id'  => $apiLoginId,
            'api_login_set' => !empty($apiLoginId),
            'tx_key_set'    => !empty($txKey),
        ]);

        $payload = [
            'createTransactionRequest' => [
                'merchantAuthentication' => [
                    'name'           => $apiLoginId,
                    'transactionKey' => $txKey,
                ],
                'refId' => (string) Str::uuid(),
                'transactionRequest' => [
                    'transactionType' => 'authCaptureTransaction',
                    'amount'          => $amount,
                    'payment'         => [
                        'opaqueData' => [
                            'dataDescriptor' => $validated['dataDescriptor'],
                            'dataValue'      => $validated['dataValue'],
                        ],
                    ],
                    'order' => [
                        'invoiceNumber' => $invoiceNumber,
                        'description'   => $planLabel,
                    ],
                    'customer' => [
                        'email' => $validated['email'],
                    ],
                    'billTo' => [
                        'firstName' => $validated['first_name'],
                        'lastName'  => $validated['last_name'],
                        'address'   => $validated['address'],
                        'city'      => $validated['city'],
                        'state'     => $validated['state'],
                        'zip'       => $validated['zip'],
                        'country'   => 'USA',
                    ],
                    'customerIP' => $request->ip(),
                ],
            ],
        ];

        Log::info('Authorize.Net payload prepared', [
            'invoice'         => $invoiceNumber,
            'refId'           => $payload['createTransactionRequest']['refId'],
            'amount'          => $amount,
            'transactionType' => 'authCaptureTransaction',
            'description'     => $planLabel,
            'email'           => $validated['email'],
            'billTo'          => $payload['createTransactionRequest']['transactionRequest']['billTo'],
            'customerIP'      => $request->ip(),
        ]);

        try {
            Log::info('Sending request to Authorize.Net', [
                'invoice'  => $invoiceNumber,
                'endpoint' => $endpoint,
            ]);

            $httpResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post($endpoint, $payload);

            Log::info('Authorize.Net raw HTTP response received', [
                'invoice'       => $invoiceNumber,
                'status'        => $httpResponse->status(),
                'successful'    => $httpResponse->successful(),
                'failed'        => $httpResponse->failed(),
                'client_error'  => $httpResponse->clientError(),
                'server_error'  => $httpResponse->serverError(),
                'headers'       => $httpResponse->headers(),
                'body'          => $httpResponse->body(),
            ]);

            $rawBody = $httpResponse->body();
            $rawBody = preg_replace('/^\xEF\xBB\xBF/', '', $rawBody);
            $rawBody = trim($rawBody);

            Log::info('Authorize.Net raw body normalized', [
                'invoice'          => $invoiceNumber,
                'raw_body_length'  => strlen($rawBody),
                'raw_body_preview' => substr($rawBody, 0, 1000),
            ]);

            $responseData = json_decode($rawBody, true);

            Log::info('Authorize.Net decoded response', [
                'invoice'         => $invoiceNumber,
                'decoded'         => $responseData,
                'json_last_error' => json_last_error_msg(),
            ]);

            $resultCode              = data_get($responseData, 'messages.resultCode');
            $transactionResponseCode = data_get($responseData, 'transactionResponse.responseCode');
            $transId                 = data_get($responseData, 'transactionResponse.transId');
            $authCode                = data_get($responseData, 'transactionResponse.authCode');

            $messageText = data_get($responseData, 'transactionResponse.messages.0.description')
                ?? data_get($responseData, 'messages.message.0.text')
                ?? 'Payment failed.';

            Log::info('Parsed Authorize.Net response values', [
                'invoice'                 => $invoiceNumber,
                'resultCode'              => $resultCode,
                'transactionResponseCode' => $transactionResponseCode,
                'transId'                 => $transId,
                'authCode'                => $authCode,
                'messageText'             => $messageText,
            ]);

            if ($resultCode === 'Ok' && $transactionResponseCode === '1') {

                Log::info('Payment successful', [
                    'invoice'       => $invoiceNumber,
                    'transId'       => $transId,
                    'authCode'      => $authCode,
                    'referral_code' => $referralCode,
                    'plan_key'      => $planKey,
                    'plan_label'    => $planLabel,
                    'amount'        => $amount,
                ]);

                // ═══════════════════════════════════════════════════════════════
                // SUBSCRIPTION FLOW — silver, gold, and platinum plans
                // ═══════════════════════════════════════════════════════════════
                $customerProfileId        = null;
                $customerPaymentProfileId = null;
                $subscriptionId           = null;

                if ($recurringAmt !== null) {

                    Log::info('Recurring subscription flow started', [
                        'invoice'      => $invoiceNumber,
                        'transId'      => $transId,
                        'plan_key'     => $planKey,
                        'plan_label'   => $planLabel,
                        'recurringAmt' => $recurringAmt,
                    ]);

                    Log::info('Creating CIM customer profile from transaction', [
                        'invoice' => $invoiceNumber,
                        'transId' => $transId,
                    ]);

                    $cimPayload = [
                        'createCustomerProfileFromTransactionRequest' => [
                            'merchantAuthentication' => [
                                'name'           => $apiLoginId,
                                'transactionKey' => $txKey,
                            ],
                            'transId'  => $transId,
                            'customer' => [
                                'email' => $validated['email'],
                            ],
                        ],
                    ];

                    Log::info('CIM payload prepared', [
                        'invoice' => $invoiceNumber,
                        'transId' => $transId,
                        'email'   => $validated['email'],
                    ]);

                    $cimResponse = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json'
                    ])->post($endpoint, $cimPayload);

                    Log::info('CIM raw HTTP response received', [
                        'invoice'    => $invoiceNumber,
                        'status'     => $cimResponse->status(),
                        'successful' => $cimResponse->successful(),
                        'failed'     => $cimResponse->failed(),
                        'body'       => $cimResponse->body(),
                    ]);

                    $cimRaw  = $cimResponse->body();
                    $cimRaw  = preg_replace('/^\xEF\xBB\xBF/', '', $cimRaw);
                    $cimData = json_decode(trim($cimRaw), true);

                    Log::info('CIM profile response', [
                        'invoice'         => $invoiceNumber,
                        'response'        => $cimData,
                        'json_last_error' => json_last_error_msg(),
                    ]);

                    $cimResultCode            = data_get($cimData, 'messages.resultCode');
                    $customerProfileId        = data_get($cimData, 'customerProfileId');
                    $customerPaymentProfileId = data_get($cimData, 'customerPaymentProfileIdList.numericString.0')
                        ?? data_get($cimData, 'customerPaymentProfileIdList.0');

                    Log::info('Parsed CIM response values', [
                        'invoice'                  => $invoiceNumber,
                        'cimResultCode'            => $cimResultCode,
                        'customerProfileId'        => $customerProfileId,
                        'customerPaymentProfileId' => $customerPaymentProfileId,
                    ]);

                    if ($cimResultCode !== 'Ok' || !$customerProfileId || !$customerPaymentProfileId) {
                        Log::error('CIM profile creation failed', [
                            'invoice'  => $invoiceNumber,
                            'response' => $cimData,
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'Your payment was captured but we could not set up your monthly subscription. Please contact support with invoice ' . $invoiceNumber . '.',
                        ], 422);
                    }

                    Log::info('CIM profile created', [
                        'invoice'                  => $invoiceNumber,
                        'customerProfileId'        => $customerProfileId,
                        'customerPaymentProfileId' => $customerPaymentProfileId,
                    ]);

                    // Allow Authorize.Net time to fully commit the CIM profile
                    // before firing the ARB subscription request (prevents E00040)
                    sleep(1);

                    $arbPayload = [
                        'ARBCreateSubscriptionRequest' => [
                            'merchantAuthentication' => [
                                'name'           => $apiLoginId,
                                'transactionKey' => $txKey,
                            ],
                            'refId'        => (string) Str::uuid(),
                            'subscription' => [
                                'name'            => $planLabel . ' Monthly',
                                'paymentSchedule' => [
                                    'interval'         => ['length' => '1', 'unit' => 'months'],
                                    'startDate'        => now()->addMonth()->format('Y-m-d'),
                                    'totalOccurrences' => '9999',
                                    'trialOccurrences' => '0',
                                ],
                                'amount'      => $recurringAmt,
                                'trialAmount' => '0.00',
                                'profile'     => [
                                    'customerProfileId'        => $customerProfileId,
                                    'customerPaymentProfileId' => $customerPaymentProfileId,
                                ],
                            ],
                        ],
                    ];

                    Log::info('ARB payload prepared', [
                        'invoice'                  => $invoiceNumber,
                        'refId'                    => $arbPayload['ARBCreateSubscriptionRequest']['refId'],
                        'subscription_name'        => $planLabel . ' Monthly',
                        'subscription_amount'      => $recurringAmt,
                        'subscription_start_date'  => now()->addMonth()->format('Y-m-d'),
                        'customerProfileId'        => $customerProfileId,
                        'customerPaymentProfileId' => $customerPaymentProfileId,
                    ]);

                    // Retry up to 3 attempts in case CIM profile is not yet
                    // fully propagated on Authorize.Net's side (E00040)
                    $arbMaxAttempts = 3;
                    $arbAttempt     = 0;
                    $arbResultCode  = null;
                    $subscriptionId = null;
                    $arbData        = null;

                    while ($arbAttempt < $arbMaxAttempts) {
                        $arbAttempt++;

                        if ($arbAttempt > 1) {
                            Log::info('ARB retry attempt', [
                                'invoice' => $invoiceNumber,
                                'attempt' => $arbAttempt,
                            ]);
                            sleep(1);
                        }

                        $arbResponse = Http::withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept'       => 'application/json'
                        ])->post($endpoint, $arbPayload);

                        Log::info('ARB raw HTTP response received', [
                            'invoice'    => $invoiceNumber,
                            'attempt'    => $arbAttempt,
                            'status'     => $arbResponse->status(),
                            'successful' => $arbResponse->successful(),
                            'failed'     => $arbResponse->failed(),
                            'body'       => $arbResponse->body(),
                        ]);

                        $arbRaw  = $arbResponse->body();
                        $arbRaw  = preg_replace('/^\xEF\xBB\xBF/', '', $arbRaw);
                        $arbData = json_decode(trim($arbRaw), true);

                        Log::info('ARB subscription response', [
                            'invoice'         => $invoiceNumber,
                            'attempt'         => $arbAttempt,
                            'response'        => $arbData,
                            'json_last_error' => json_last_error_msg(),
                        ]);

                        $arbResultCode  = data_get($arbData, 'messages.resultCode');
                        $subscriptionId = data_get($arbData, 'subscriptionId');

                        Log::info('Parsed ARB response values', [
                            'invoice'        => $invoiceNumber,
                            'attempt'        => $arbAttempt,
                            'arbResultCode'  => $arbResultCode,
                            'subscriptionId' => $subscriptionId,
                        ]);

                        if ($arbResultCode === 'Ok' && $subscriptionId) {
                            break; // Success — exit retry loop
                        }

                        Log::warning('ARB attempt failed, will retry if attempts remain', [
                            'invoice'        => $invoiceNumber,
                            'attempt'        => $arbAttempt,
                            'arbResultCode'  => $arbResultCode,
                            'subscriptionId' => $subscriptionId,
                            'response'       => $arbData,
                        ]);
                    }

                    if ($arbResultCode !== 'Ok' || !$subscriptionId) {
                        Log::error('ARB subscription creation failed after all attempts', [
                            'invoice'      => $invoiceNumber,
                            'attempts'     => $arbAttempt,
                            'response'     => $arbData,
                        ]);

                        return response()->json([
                            'success' => false,
                            'message' => 'Your payment was captured but the monthly subscription could not be created. Please contact support with invoice ' . $invoiceNumber . '.',
                        ], 422);
                    }

                    Log::info('ARB subscription created successfully', [
                        'invoice'        => $invoiceNumber,
                        'attempt'        => $arbAttempt,
                        'subscriptionId' => $subscriptionId,
                        'recurringAmt'   => $recurringAmt,
                        'startDate'      => now()->addMonth()->format('Y-m-d'),
                    ]);

                } else {
                    Log::info('Recurring subscription skipped because selected plan is one-time', [
                        'invoice'    => $invoiceNumber,
                        'plan_key'   => $planKey,
                        'plan_label' => $planLabel,
                    ]);
                }
                // ═══════════════════════════════════════════════════════════════

                // ─────────────────────────────────────────────────────────────
                // SAVE SUBSCRIPTION TO DATABASE
                // ─────────────────────────────────────────────────────────────
                try {
                    Subscription::create([
                        'first_name'                  => $validated['first_name'],
                        'last_name'                   => $validated['last_name'],
                        'email'                       => $validated['email'],
                        'phone'                       => $validated['phone'],
                        'address'                     => $validated['address'],
                        'city'                        => $validated['city'],
                        'state'                       => $validated['state'],
                        'zip'                         => $validated['zip'],
                        'plan_key'                    => $planKey,
                        'plan_label'                  => $planLabel,
                        'amount'                      => $amount,
                        'recurring_amount'            => $recurringAmt,
                        'invoice_number'              => $invoiceNumber,
                        'transaction_id'              => $transId,
                        'auth_code'                   => $authCode,
                        'arb_subscription_id'         => $subscriptionId ?? null,
                        'customer_profile_id'         => $customerProfileId ?? null,
                        'customer_payment_profile_id' => $customerPaymentProfileId ?? null,
                        'referral_code'               => $referralCode,
                        'status'                      => 'active',
                        'subscribed_at'               => now(),
                        'next_billing_date'           => now()->addMonth(),
                    ]);

                    Log::info('Subscription saved to database', [
                        'invoice'        => $invoiceNumber,
                        'arb_sub_id'     => $subscriptionId ?? null,
                    ]);
                } catch (\Throwable $dbEx) {
                    Log::error('Failed to save subscription to database', [
                        'invoice' => $invoiceNumber,
                        'error'   => $dbEx->getMessage(),
                    ]);
                }
                // ─────────────────────────────────────────────────────────────

                session([
                    'acceptjs_payment_success' => true,
                    'acceptjs_invoice_number'  => $invoiceNumber,
                    'acceptjs_transaction_id'  => $transId,
                    'acceptjs_auth_code'       => $authCode,
                    'acceptjs_customer'        => [
                        'first_name' => $validated['first_name'],
                        'last_name'  => $validated['last_name'],
                        'email'      => $validated['email'],
                        'phone'      => $validated['phone'],
                        'address'    => $validated['address'],
                        'city'       => $validated['city'],
                        'state'      => $validated['state'],
                        'zip'        => $validated['zip'],
                    ],
                ]);

                Log::info('Success session stored', [
                    'invoice'        => $invoiceNumber,
                    'transaction_id' => $transId,
                    'auth_code'      => $authCode,
                    'session_keys'   => [
                        'acceptjs_payment_success',
                        'acceptjs_invoice_number',
                        'acceptjs_transaction_id',
                        'acceptjs_auth_code',
                        'acceptjs_customer',
                    ],
                ]);

                Cache::put('checkout_customer_' . $invoiceNumber, [
                    'first_name'    => $validated['first_name'],
                    'last_name'     => $validated['last_name'],
                    'email'         => $validated['email'],
                    'phone'         => $validated['phone'],
                    'address'       => $validated['address'],
                    'city'          => $validated['city'],
                    'state'         => $validated['state'],
                    'zip'           => $validated['zip'],
                    'plan_key'      => $planKey,
                    'plan_label'    => $planLabel,
                    'amount'        => $amount,
                    'referral_code' => $referralCode,
                ], now()->addMinutes(120));

                Log::info('Checkout customer cache stored', [
                    'invoice'       => $invoiceNumber,
                    'cache_key'     => 'checkout_customer_' . $invoiceNumber,
                    'cache_ttl'     => '60 minutes',
                    'referral_code' => $referralCode,
                    'plan_key'      => $planKey,
                    'amount'        => $amount,
                ]);

                // ─────────────────────────────────────────────────────────────
                // Fire GHL webhook immediately
                // ─────────────────────────────────────────────────────────────
                $this->fireGhlWebhook($invoiceNumber, $transId, $referralCode, [
                    'first_name' => $validated['first_name'],
                    'last_name'  => $validated['last_name'],
                    'email'      => $validated['email'],
                    'phone'      => $validated['phone'],
                    'address'    => $validated['address'],
                    'city'       => $validated['city'],
                    'state'      => $validated['state'],
                    'zip'        => $validated['zip'],
                    'plan_label' => $planLabel,
                    'plan_key'   => $planKey,
                    'amount'     => $amount,
                ]);
                // ─────────────────────────────────────────────────────────────

                // ─────────────────────────────────────────────────────────────
                // META CONVERSIONS API — fire Purchase event server-side
                // ─────────────────────────────────────────────────────────────
                $this->fireMetaCapi($invoiceNumber, $transId, $amount, $validated, $request->ip(), $request->userAgent());
                // ─────────────────────────────────────────────────────────────

                Log::info('Accept.js success session + cache stored', [
                    'invoice'        => $invoiceNumber,
                    'transaction_id' => $transId,
                    'referral_code'  => $referralCode,
                    'redirect'       => url('/onboardingform'),
                ]);

                return response()->json([
                    'success'     => true,
                    'message'     => 'Payment successful.',
                    'invoice'     => $invoiceNumber,
                    'transaction' => $transId,
                    'redirect'    => url('/onboardingform'),
                ]);
            }

            $transactionErrors = data_get($responseData, 'transactionResponse.errors', []);

            Log::warning('Payment failed at Authorize.Net', [
                'invoice'            => $invoiceNumber,
                'message'            => $messageText,
                'resultCode'         => $resultCode,
                'responseCode'       => $transactionResponseCode,
                'transaction_errors' => $transactionErrors,
                'response'           => $responseData,
            ]);

            return response()->json([
                'success'            => false,
                'message'            => $messageText,
                'transaction_errors' => $transactionErrors,
                'response'           => $responseData,
            ], 422);

        } catch (\Throwable $e) {

            Log::error('Accept.js payment exception', [
                'invoice'    => $invoiceNumber ?? 'N/A',
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'trace'      => $e->getTraceAsString(),
                'request_ip' => $request->ip(),
                'email'      => $request->input('email'),
                'plan'       => $request->input('selected_plan'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GHL WEBHOOK DISPATCHER
    // ─────────────────────────────────────────────────────────────────────────
    private function fireGhlWebhook(string $invoice, string $transId, ?string $referralCode, array $customer): void
    {
        if ($referralCode) {

            $url = config('services.ghl.referral_webhook_url');

            $payload = [
                'first_name'     => $customer['first_name'],
                'last_name'      => $customer['last_name'],
                'email'          => $customer['email'],
                'phone'          => $customer['phone'],
                'address'        => $customer['address'],
                'city'           => $customer['city'],
                'state'          => $customer['state'],
                'zip'            => $customer['zip'],
                'plan'           => $customer['plan_label'],
                'plan_key'       => $customer['plan_key'],
                'amount'         => $customer['amount'],
                'invoice_number' => $invoice,
                'transaction_id' => $transId,
                'referral_code'  => $referralCode,
                'source'         => 'referral_partner',
                'tags'           => [
                    'referral-partner',
                    'partner-' . strtolower(str_replace('PARTNER-', '', $referralCode)),
                ],
            ];

            Log::info('Firing GHL REFERRAL webhook', [
                'invoice'       => $invoice,
                'referral_code' => $referralCode,
                'email'         => $customer['email'],
            ]);

        } else {

            $url = config('services.ghl.checkout_webhook_url');

            $payload = [
                'first_name'     => $customer['first_name'],
                'last_name'      => $customer['last_name'],
                'email'          => $customer['email'],
                'phone'          => $customer['phone'],
                'address'        => $customer['address'],
                'city'           => $customer['city'],
                'state'          => $customer['state'],
                'zip'            => $customer['zip'],
                'plan'           => $customer['plan_label'],
                'amount'         => $customer['amount'],
                'invoice_number' => $invoice,
                'transaction_id' => $transId,
                'source'         => '850_fico_checkout',
            ];

            Log::info('Firing GHL MAIN checkout webhook', [
                'invoice' => $invoice,
                'email'   => $customer['email'],
            ]);

        }

        if (!$url) {
            Log::warning('GHL webhook URL not set in .env', [
                'invoice'       => $invoice,
                'referral_code' => $referralCode,
            ]);
            return;
        }

        try {
            Http::timeout(15)->post($url, $payload);
            Cache::put('ghl_webhook_fired_' . $invoice, true, now()->addMinutes(60));
            Log::info('GHL webhook fired successfully', [
                'invoice'       => $invoice,
                'url'           => $url,
                'referral_code' => $referralCode,
            ]);
        } catch (\Throwable $e) {
            Log::error('GHL webhook failed', [
                'invoice' => $invoice,
                'url'     => $url,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // META CONVERSIONS API — server-side Purchase event
    // Fires after every confirmed payment.
    // Reads META_PIXEL_ID and META_CAPI_TOKEN from .env.
    // Never throws — logs errors and returns silently.
    // ─────────────────────────────────────────────────────────────────────────
    private function fireMetaCapi(
        string  $invoice,
        string  $transId,
        string  $amount,
        array   $validated,
        string  $ip,
        ?string $userAgent
    ): void {
        $pixelId    = config('services.meta.pixel_id');
        $capiToken  = config('services.meta.capi_token');

        if (!$pixelId || !$capiToken) {
            Log::warning('Meta CAPI skipped — META_PIXEL_ID or META_CAPI_TOKEN not set in .env', [
                'invoice' => $invoice,
            ]);
            return;
        }

        // ── Hash PII per Meta spec (SHA-256, lowercase, trimmed) ──────────────
        $hashedEmail = hash('sha256', strtolower(trim($validated['email'])));
        $hashedPhone = hash('sha256', preg_replace('/\D/', '', trim($validated['phone'] ?? '')));
        $hashedFn    = hash('sha256', strtolower(trim($validated['first_name'] ?? '')));
        $hashedLn    = hash('sha256', strtolower(trim($validated['last_name'] ?? '')));
        $hashedZip   = hash('sha256', trim($validated['zip'] ?? ''));
        $hashedCity  = hash('sha256', strtolower(trim($validated['city'] ?? '')));
        $hashedState = hash('sha256', strtolower(trim($validated['state'] ?? '')));

        $eventPayload = [
            'data' => [
                [
                    'event_name'    => 'Purchase',
                    'event_time'    => time(),
                    'action_source' => 'website',
                    'event_id'      => 'purchase_' . $transId, // dedup key
                    'user_data'     => [
                        'em'         => [$hashedEmail],
                        'ph'         => [$hashedPhone],
                        'fn'         => [$hashedFn],
                        'ln'         => [$hashedLn],
                        'zp'         => [$hashedZip],
                        'ct'         => [$hashedCity],
                        'st'         => [$hashedState],
                        'client_ip_address'  => $ip,
                        'client_user_agent'  => $userAgent ?? '',
                    ],
                    'custom_data'   => [
                        'currency' => 'USD',
                        'value'    => $amount,
                        'order_id' => $invoice,
                    ],
                ],
            ],
        ];

        $url = "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$capiToken}";

        Log::info('Firing Meta CAPI Purchase event', [
            'invoice'    => $invoice,
            'trans_id'   => $transId,
            'amount'     => $amount,
            'pixel_id'   => $pixelId,
            'event_id'   => 'purchase_' . $transId,
        ]);

        try {
            $capiResponse = Http::timeout(10)->post($url, $eventPayload);

            Log::info('Meta CAPI response', [
                'invoice' => $invoice,
                'status'  => $capiResponse->status(),
                'body'    => $capiResponse->body(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Meta CAPI request failed', [
                'invoice' => $invoice,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}