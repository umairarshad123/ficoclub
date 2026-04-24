<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FundingFormController extends Controller
{
    // ── Allowed extensions ────────────────────────────────────
    private array $allowedExtensions = [
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf',
    ];

    // ── Upload directory (no Flysystem, no finfo needed) ─────
    private function uploadDir(): string
    {
        $dir = storage_path('app/lead_uploads');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }

    // ── Main submit handler ───────────────────────────────────
    public function submit(Request $request)
    {
        \Log::info('[FundingForm] Submit started', [
            'ip'             => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'email'          => $request->email,
            'phone'          => $request->phone,
            'has_drivers_id' => $request->hasFile('drivers_id'),
            'has_articles'   => $request->hasFile('articles'),
            'has_ein_letter' => $request->hasFile('ein_letter'),
        ]);

        try {
            // ── Parse DOB ─────────────────────────────────────
            \Log::info('[FundingForm] Parsing DOB', [
                'dob_month' => $request->dob_month,
                'dob_day'   => $request->dob_day,
                'dob_year'  => $request->dob_year,
            ]);

            $dob = null;
            if ($request->dob_month && $request->dob_day && $request->dob_year) {
                $months = [
                    'January'=>1,'February'=>2,'March'=>3,'April'=>4,
                    'May'=>5,'June'=>6,'July'=>7,'August'=>8,
                    'September'=>9,'October'=>10,'November'=>11,'December'=>12,
                ];
                $m   = $months[$request->dob_month] ?? (int)$request->dob_month;
                $dob = sprintf('%04d-%02d-%02d',
                    (int)$request->dob_year, $m, (int)$request->dob_day
                );
            }

            \Log::info('[FundingForm] DOB parsed', ['dob' => $dob]);

            // ── Save lead ─────────────────────────────────────
            \Log::info('[FundingForm] Creating lead record');

            $lead = Lead::create([
                'first_name'            => $request->first_name,
                'last_name'             => $request->last_name,
                'phone'                 => $request->phone,
                'email'                 => $request->email,
                'ssn_encrypted'         => $this->encrypt($request->ssn ?? ''),
                'dob'                   => $dob,
                'employment_status'     => $request->employment_status,
                'annual_income'         => $request->annual_income,
                'own_or_rent'           => $request->own_or_rent,
                'monthly_housing'       => $request->monthly_housing,
                'address'               => $request->address,
                'city'                  => $request->city,
                'state'                 => $request->state,
                'zip'                   => $request->zip,
                'years_at_address'      => $request->years_at_address,
                'sc_email'              => $request->sc_email,
                'sc_password_encrypted' => $this->encrypt($request->sc_password ?? ''),
                'sc_ssn_last4'          => $request->sc_ssn_last4,
                'funding_type'          => $request->funding_type ?: null,
                'biz_entity_type'       => $request->biz_entity_type ?: null,
                'biz_name'              => $request->biz_name ?: null,
                'biz_phone'             => $request->biz_phone ?: null,
                'biz_email'             => $request->biz_email ?: null,
                'biz_website'           => $request->biz_website ?: null,
                'biz_incorp_state'      => $request->biz_incorp_state ?: null,
                'biz_has_directors'     => $request->biz_has_directors ?: 'Not specified',
                'biz_annual_revenue'    => $request->biz_annual_revenue ?: null,
                'biz_has_financials'    => $request->biz_has_financials ?: 'Not specified',
                'biz_address'           => $request->biz_address ?: null,
                'biz_city'              => $request->biz_city ?: null,
                'biz_state'             => $request->biz_state ?: null,
                'biz_zip'               => $request->biz_zip ?: null,
                'final_phone'           => $request->final_phone,
                'final_email'           => $request->final_email,
                'ip_address'            => $request->ip(),
                'user_agent'            => $request->userAgent(),
            ]);

            \Log::info('[FundingForm] Lead created', [
                'lead_id'   => $lead->id,
                'lead_uuid' => $lead->uuid,
            ]);

            // ── Handle file uploads ───────────────────────────
            $uploadedFiles = [];
            $fileFields = [
                'drivers_id' => 'drivers_id',
                'articles'   => 'articles',
                'ein_letter' => 'ein_letter',
            ];

            \Log::info('[FundingForm] Starting file upload handling', [
                'fields' => array_keys($fileFields),
            ]);

            foreach ($fileFields as $inputName => $fileKey) {
                \Log::info('[FundingForm] Checking file field', [
                    'input_name' => $inputName,
                    'file_key'   => $fileKey,
                    'has_file'   => $request->hasFile($inputName),
                ]);

                if (!$request->hasFile($inputName)) continue;

                $file     = $request->file($inputName);
                $ext      = strtolower($file->getClientOriginalExtension());
                $mimeType = $file->getClientMimeType();

                // ── Capture these BEFORE move() deletes the temp file ──
                $originalName = $file->getClientOriginalName();
                $fileSize     = $file->getSize();

                \Log::info('[FundingForm] File received', [
                    'input_name'    => $inputName,
                    'original_name' => $originalName,
                    'extension'     => $ext,
                    'mime_type'     => $mimeType,
                    'size'          => $fileSize,
                    'is_valid'      => $file->isValid(),
                ]);

                if (!$file->isValid()) {
                    \Log::warning('[FundingForm] File invalid', ['input_name' => $inputName]);
                    continue;
                }

                if (!in_array($ext, $this->allowedExtensions)) {
                    \Log::warning('[FundingForm] File extension not allowed', [
                        'input_name' => $inputName,
                        'extension'  => $ext,
                    ]);
                    continue;
                }

                if ($fileSize > 10 * 1024 * 1024) {
                    \Log::warning('[FundingForm] File too large', [
                        'input_name' => $inputName,
                        'size'       => $fileSize,
                    ]);
                    continue;
                }

                // ── move() bypasses Flysystem/finfo entirely ──
                $storedName = Str::uuid() . '.' . $ext;
                $file->move($this->uploadDir(), $storedName);

                \Log::info('[FundingForm] File stored', [
                    'input_name'  => $inputName,
                    'stored_name' => $storedName,
                ]);

                // Generate signed download token
                $token       = hash_hmac('sha256',
                    $lead->uuid . '|' . $fileKey . '|' . time(),
                    config('app.download_secret')
                );
                $downloadUrl = config('app.download_url') . '?token=' . urlencode($token);

                \Log::info('[FundingForm] Download token and URL generated', [
                    'input_name'   => $inputName,
                    'file_key'     => $fileKey,
                    'download_url' => $downloadUrl,
                ]);

                // Save file record — use pre-captured values, not $file (temp gone after move)
                LeadFile::create([
                    'lead_id'        => $lead->id,
                    'file_key'       => $fileKey,
                    'original_name'  => $originalName,
                    'stored_name'    => $storedName,
                    'mime_type'      => $mimeType,
                    'file_size'      => $fileSize,
                    'download_token' => $token,
                    'download_url'   => $downloadUrl,
                ]);

                \Log::info('[FundingForm] LeadFile record created', [
                    'lead_id'       => $lead->id,
                    'file_key'      => $fileKey,
                    'original_name' => $originalName,
                ]);

                $uploadedFiles[$fileKey] = [
                    'url'  => $downloadUrl,
                    'name' => $originalName,
                ];
            }

            \Log::info('[FundingForm] File handling completed', [
                'uploaded_files' => array_keys($uploadedFiles),
            ]);

            // ── Build payload for Sheets + GHL ────────────────
            $payload = [
                'submitted_at'        => now()->toDateTimeString(),
                'lead_uuid'           => $lead->uuid,
                'first_name'          => $request->first_name,
                'last_name'           => $request->last_name,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'ssn'                 => $request->ssn,
                'dob'                 => $dob ?? '',
                'employment_status'   => $request->employment_status,
                'annual_income'       => $request->annual_income,
                'own_or_rent'         => $request->own_or_rent,
                'monthly_housing'     => $request->monthly_housing,
                'address'             => $request->address,
                'city'                => $request->city,
                'state'               => $request->state,
                'zip'                 => $request->zip,
                'years_at_address'    => $request->years_at_address,
                'sc_email'            => $request->sc_email,
                'sc_password'         => $request->sc_password,
                'sc_ssn_last4'        => $request->sc_ssn_last4,
                'funding_type'        => $request->funding_type,
                'biz_entity_type'     => $request->biz_entity_type,
                'biz_name'            => $request->biz_name,
                'biz_phone'           => $request->biz_phone,
                'biz_email'           => $request->biz_email,
                'biz_website'         => $request->biz_website,
                'biz_incorp_state'    => $request->biz_incorp_state,
                'biz_has_directors'   => $request->biz_has_directors,
                'biz_annual_revenue'  => $request->biz_annual_revenue,
                'biz_has_financials'  => $request->biz_has_financials,
                'biz_address'         => $request->biz_address,
                'biz_city'            => $request->biz_city,
                'biz_state'           => $request->biz_state,
                'biz_zip'             => $request->biz_zip,
                'final_phone'         => $request->final_phone,
                'final_email'         => $request->final_email,
                'file_drivers_id_url' => $uploadedFiles['drivers_id']['url'] ?? '',
                'file_articles_url'   => $uploadedFiles['articles']['url'] ?? '',
                'file_ein_letter_url' => $uploadedFiles['ein_letter']['url'] ?? '',
                'ip_address'          => $request->ip(),
            ];

            \Log::info('[FundingForm] Payload built for Sheets and GHL', [
                'lead_uuid'          => $payload['lead_uuid'],
                'email'              => $payload['email'],
                'phone'              => $payload['phone'],
                'funding_type'       => $payload['funding_type'],
                'has_drivers_id_url' => !empty($payload['file_drivers_id_url']),
                'has_articles_url'   => !empty($payload['file_articles_url']),
                'has_ein_letter_url' => !empty($payload['file_ein_letter_url']),
            ]);

            // ── Push to Google Sheets ─────────────────────────
            \Log::info('[FundingForm] Pushing to Google Sheets', ['lead_uuid' => $lead->uuid]);
            $sheetsOk = $this->pushToGoogleSheets($payload);
            \Log::info('[FundingForm] Google Sheets push completed', [
                'lead_uuid' => $lead->uuid,
                'success'   => $sheetsOk,
            ]);
            if ($sheetsOk) {
                $lead->update(['sheets_pushed' => true]);
                \Log::info('[FundingForm] Lead updated: sheets_pushed=true', ['lead_id' => $lead->id]);
            }

            // ── Push to GHL ───────────────────────────────────
            \Log::info('[FundingForm] Pushing to GHL', ['lead_uuid' => $lead->uuid]);
            $ghlOk = $this->pushToGhl($payload);
            \Log::info('[FundingForm] GHL push completed', [
                'lead_uuid' => $lead->uuid,
                'success'   => $ghlOk,
            ]);
            if ($ghlOk) {
                $lead->update(['ghl_pushed' => true]);
                \Log::info('[FundingForm] Lead updated: ghl_pushed=true', ['lead_id' => $lead->id]);
            }

            \Log::info('[FundingForm] Submit completed successfully', [
                'lead_id'   => $lead->id,
                'lead_uuid' => $lead->uuid,
                'sheets_ok' => $sheetsOk,
                'ghl_ok'    => $ghlOk,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application received! A specialist will contact you within 24 hours.',
            ]);

        } catch (\Throwable $e) {
            \Log::error('[FundingForm] Submit failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    // ── Secure file download ──────────────────────────────────
    public function download(Request $request)
    {
        $token = trim((string) $request->query('token', ''));

        \Log::info('[FundingForm] Download requested', [
            'token_present' => !empty($token),
            'token_length'  => strlen($token),
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        if (!$token || strlen($token) !== 64) {
            \Log::warning('[FundingForm] Invalid download link format', [
                'token_length' => strlen($token),
            ]);
            abort(404, 'Invalid download link.');
        }

        $file = LeadFile::where('download_token', $token)->first();

        if (!$file) {
            \Log::warning('[FundingForm] Download token not found', ['token' => $token]);
            abort(404, 'Download link is invalid or expired.');
        }

        // ── Path matches where move() saved the file ──────────
        $path = storage_path('app/lead_uploads' . DIRECTORY_SEPARATOR . basename($file->stored_name));

        \Log::info('[FundingForm] Download file record found', [
            'lead_id'       => $file->lead_id,
            'file_key'      => $file->file_key,
            'stored_name'   => $file->stored_name,
            'original_name' => $file->original_name,
            'path'          => $path,
        ]);

        if (!file_exists($path)) {
            \Log::error('[FundingForm] File missing on server', [
                'path'        => $path,
                'stored_name' => $file->stored_name,
            ]);
            abort(404, 'File not found on server.');
        }

        \Log::info('[FundingForm] Download starting', [
            'lead_id'       => $file->lead_id,
            'file_key'      => $file->file_key,
            'original_name' => $file->original_name,
        ]);

        return response()->download($path, $file->original_name, [
            'Content-Type'  => $file->mime_type,
            'Cache-Control' => 'private, no-cache',
        ]);
    }

    // ── Encrypt sensitive fields ──────────────────────────────
    private function encrypt(string $value): string
    {
        \Log::info('[FundingForm] Encrypt called', [
            'has_value'    => $value !== '',
            'value_length' => strlen($value),
        ]);

        if ($value === '') return '';

        $iv  = random_bytes(16);
        $enc = openssl_encrypt(
            $value, 'AES-256-CBC',
            config('app.download_secret'),
            OPENSSL_RAW_DATA, $iv
        );

        \Log::info('[FundingForm] Encrypt completed', ['encrypted' => $enc !== false]);

        return base64_encode($iv . $enc);
    }

    // ── Google Sheets push ────────────────────────────────────
    private function pushToGoogleSheets(array $row): bool
    {
        \Log::info('[FundingForm] pushToGoogleSheets called', [
            'lead_uuid' => $row['lead_uuid'] ?? null,
            'sheet_id'  => config('services.google.sheet_id'),
        ]);

        try {
            $token = $this->getGoogleAccessToken();

            \Log::info('[FundingForm] Google access token result', [
                'token_received' => !empty($token),
            ]);

            if (!$token) return false;

            $url = sprintf(
                'https://sheets.googleapis.com/v4/spreadsheets/%s/values/%s:append?valueInputOption=USER_ENTERED&insertDataOption=INSERT_ROWS',
                urlencode(config('services.google.sheet_id')),
                urlencode('A1')
            );

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode(['values' => [array_values($row)]]),
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

            \Log::info('[FundingForm] Google Sheets response', [
                'status'     => $status,
                'curl_error' => $curlError,
                'response'   => $resp,
            ]);

            return $status >= 200 && $status < 300;
        } catch (\Throwable $e) {
            \Log::error('[FundingForm] Sheets error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    // ── GHL webhook push ──────────────────────────────────────
    private function pushToGhl(array $row): bool
    {
        $url = config('services.ghl.funding_webhook_url');

        \Log::info('[FundingForm] pushToGhl called', [
            'lead_uuid'   => $row['lead_uuid'] ?? null,
            'webhook_url' => $url,
        ]);

        if (!$url) {
            \Log::warning('[FundingForm] GHL funding webhook URL missing — check GHL_FUNDING_WEBHOOK_URL in .env');
            return false;
        }

        try {
            $payload = [
                'firstName'  => $row['first_name'],
                'lastName'   => $row['last_name'],
                'phone'      => $row['final_phone'] ?: $row['phone'],
                'email'      => $row['final_email'] ?: $row['email'],
                'address1'   => $row['address'],
                'city'       => $row['city'],
                'state'      => $row['state'],
                'postalCode' => $row['zip'],
                'source'     => '850 FICO Club — Business Funding Form',
                'tags'       => ['850fico-lead', 'funding-' . ($row['funding_type'] ?: 'unknown')],
                'customData' => $row,
            ];

            \Log::info('[FundingForm] GHL payload prepared', [
                'firstName' => $payload['firstName'],
                'lastName'  => $payload['lastName'],
                'phone'     => $payload['phone'],
                'email'     => $payload['email'],
                'source'    => $payload['source'],
                'tags'      => $payload['tags'],
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 15,
            ]);
            $resp      = curl_exec($ch);
            $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            \Log::info('[FundingForm] GHL response', [
                'status'     => $status,
                'curl_error' => $curlError,
                'response'   => $resp,
            ]);

            return $status >= 200 && $status < 300;
        } catch (\Throwable $e) {
            \Log::error('[FundingForm] GHL error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    // ── Google OAuth token ────────────────────────────────────
    private function getGoogleAccessToken(): ?string
    {
        $credPath = base_path(config('services.google.credentials_path'));

        \Log::info('[FundingForm] getGoogleAccessToken called', [
            'credentials_path' => $credPath,
        ]);

        if (!file_exists($credPath)) {
            \Log::error('[FundingForm] Google credentials not found: ' . $credPath);
            return null;
        }

        $creds = json_decode(file_get_contents($credPath), true);

        \Log::info('[FundingForm] Google credentials loaded', [
            'loaded'       => !empty($creds),
            'client_email' => $creds['client_email'] ?? null,
        ]);

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
        $jwt    = $input . '.' . $this->base64url($sig);

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
        $rawResp   = curl_exec($ch);
        $curlError = curl_error($ch);
        $resp      = json_decode($rawResp, true);
        curl_close($ch);

        \Log::info('[FundingForm] Google token response', [
            'curl_error' => $curlError,
            'response'   => $resp,
        ]);

        return $resp['access_token'] ?? null;
    }

    private function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}