<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FundingReadyFormController extends Controller
{
    // ── Column headers that match the Google Sheet ────────────────
    // Row 1 of your sheet should have these headers in order:
    // submitted_at | funding_amount_goal | accuracy_confirmed |
    // credit_card_limit_15k | credit_utilization | fico_score_range |
    // business_situation | annual_income | negative_marks |
    // first_name | last_name | phone | email | lead_type | page_source | ip_address

    public function submit(Request $request)
    {
        \Log::info('[FundingReadyForm] Submit received', [
            'ip'          => $request->ip(),
            'page_source' => $request->page_source,
            'email'       => $request->email,
            'lead_type'   => $request->lead_type,
        ]);

        try {
            $data = $request->only([
                'funding_amount_goal',
                'accuracy_confirmed',
                'credit_card_limit_15k',
                'credit_utilization',
                'fico_score_range',
                'business_situation',
                'annual_income',
                'negative_marks',
                'first_name',
                'last_name',
                'phone',
                'email',
                'lead_type',
                'page_source',
                'submitted_at',
            ]);

            $row = [
                $data['submitted_at']        ?? now()->toDateTimeString(),
                $data['funding_amount_goal'] ?? '',
                $data['accuracy_confirmed']  ?? '',
                $data['credit_card_limit_15k'] ?? '',
                $data['credit_utilization']  ?? '',
                $data['fico_score_range']    ?? '',
                $data['business_situation']  ?? '',
                $data['annual_income']       ?? '',
                $data['negative_marks']      ?? '',
                $data['first_name']          ?? '',
                $data['last_name']           ?? '',
                $data['phone']               ?? '',
                $data['email']               ?? '',
                $data['lead_type']           ?? '',
                $data['page_source']         ?? '',
                $request->ip(),
            ];

            \Log::info('[FundingReadyForm] Row built', ['row' => $row]);

            $sheetsOk = $this->pushToGoogleSheets($row);

            \Log::info('[FundingReadyForm] Sheets push result', ['success' => $sheetsOk]);

            // Optionally push to GHL as well
            if (config('services.ghl.funding_webhook_url')) {
                $this->pushToGhl($data, $request->ip());
            }

            return response()->json([
                'success' => true,
                'message' => 'Application received!',
            ]);

        } catch (\Throwable $e) {
            \Log::error('[FundingReadyForm] Submit error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    // ── Google Sheets push ────────────────────────────────────────
    private function pushToGoogleSheets(array $row): bool
    {
        \Log::info('[FundingReadyForm] pushToGoogleSheets called');

        try {
            $token = $this->getGoogleAccessToken();

            if (!$token) {
                \Log::error('[FundingReadyForm] No Google access token');
                return false;
            }

            $sheetId = config('services.google.ready_sheet_id',
                         config('services.google.sheet_id'));

            $url = sprintf(
                'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS',
                urlencode($sheetId),
                urlencode('Sheet1!A1')
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode(['values' => [$row]]),
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $token,
                    'Content-Type: application/json',
                ],
                CURLOPT_TIMEOUT => 15,
            ]);

            $resp      = curl_exec($ch);
            $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            \Log::info('[FundingReadyForm] Sheets response', [
                'status'     => $status,
                'curl_error' => $curlError,
                'response'   => $resp,
            ]);

            return $status >= 200 && $status < 300;

        } catch (\Throwable $e) {
            \Log::error('[FundingReadyForm] Sheets error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Optional GHL webhook push ─────────────────────────────────
    private function pushToGhl(array $data, string $ip): bool
    {
        $url = config('services.ghl.funding_webhook_url');
        if (!$url) return false;

        try {
            $leadType = $data['lead_type'] ?? 'unknown';
            $payload  = [
                'firstName'  => $data['first_name']  ?? '',
                'lastName'   => $data['last_name']   ?? '',
                'phone'      => $data['phone']        ?? '',
                'email'      => $data['email']        ?? '',
                'source'     => '850 FICO Club — Funding Ready Form',
                'tags'       => ['850fico-lead', 'funding-ready-' . $leadType],
                'customData' => array_merge($data, ['ip_address' => $ip]),
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 15,
            ]);
            $resp   = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            \Log::info('[FundingReadyForm] GHL response', ['status' => $status]);
            return $status >= 200 && $status < 300;

        } catch (\Throwable $e) {
            \Log::error('[FundingReadyForm] GHL error: ' . $e->getMessage());
            return false;
        }
    }

    // ── Google OAuth (same pattern as FundingFormController) ──────
    private function getGoogleAccessToken(): ?string
    {
        $credPath = base_path(config('services.google.credentials_path'));

        if (!file_exists($credPath)) {
            \Log::error('[FundingReadyForm] Google credentials not found: ' . $credPath);
            return null;
        }

        $creds = json_decode(file_get_contents($credPath), true);
        if (!$creds) return null;

        $now   = time();
        $claim = [
            'iss'   => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/spreadsheets',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $header = $this->base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->base64url(json_encode($claim));
        $input  = $header . '.' . $claims;
        openssl_sign($input, $sig, $creds['private_key'], 'SHA256');
        $jwt = $input . '.' . $this->base64url($sig);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_TIMEOUT => 10,
        ]);
        $rawResp = curl_exec($ch);
        $resp    = json_decode($rawResp, true);
        curl_close($ch);

        return $resp['access_token'] ?? null;
    }

    private function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}