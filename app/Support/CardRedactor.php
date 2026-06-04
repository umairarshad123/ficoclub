<?php

namespace App\Support;

/**
 * PCI-safety helper: scrub any card-sensitive fields out of a payload
 * before it is rendered in the admin UI.
 *
 * Card data still lives in the Authorize.Net charge request and in the
 * attached Google Sheet — but it MUST NEVER surface in:
 *   - admin dashboard views
 *   - webhook payload viewers
 *   - public site / front-end
 *   - log files we display to humans
 *
 * Usage:
 *   $clean = CardRedactor::redact($payload);
 *   echo json_encode($clean, JSON_PRETTY_PRINT);
 */
class CardRedactor
{
    /**
     * Keys whose values are always replaced with [REDACTED].
     * Matched case-insensitively against the array key.
     */
    private const SENSITIVE_KEYS = [
        // Full PAN
        'cardnumber',
        'card_number',
        'pan',
        // CVV / CVC / security code
        'cardcode',
        'card_cvv',
        'cvv',
        'cvc',
        'cvv2',
        'securitycode',
        'security_code',
        // Expiration date in any shape
        'expirationdate',
        'expiration_date',
        'expdate',
        'exp_date',
        'expmonth',
        'expyear',
        'exp_month',
        'exp_year',
        'card_exp',
        // Auth.net masked account number — already X'd but defensive
        'accountnumber',
        // Magnetic stripe data
        'track1',
        'track2',
        // Accept.js opaque token + descriptor (treated as sensitive)
        'datavalue',
        'datadescriptor',
        // Card-holder name (not strictly PCI but card-related)
        'cardname',
        'card_name',
        'nameonaccount',
    ];

    /**
     * Walk an arbitrary structure (array / scalar) and redact card data.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function redact($value)
    {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                if (is_string($k) && in_array(strtolower($k), self::SENSITIVE_KEYS, true)) {
                    $out[$k] = '[REDACTED]';
                    continue;
                }
                $out[$k] = self::redact($v);
            }
            return $out;
        }

        // Catch raw PANs that may be loose strings (13–19 digits, possibly
        // separated by spaces or dashes). Mask everything except last 4.
        if (is_string($value)) {
            $digits = preg_replace('/\D/', '', $value);
            if ($digits !== null && strlen($digits) >= 13 && strlen($digits) <= 19) {
                // Quick Luhn check so we don't mask transaction IDs that
                // happen to be in that length range.
                if (self::passesLuhn($digits)) {
                    return '****' . substr($digits, -4);
                }
            }
        }

        return $value;
    }

    /**
     * Luhn-mod-10 check. Returns true if the string looks like a real PAN.
     */
    private static function passesLuhn(string $digits): bool
    {
        $sum = 0;
        $alt = false;
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            $n = (int) $digits[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        return $sum % 10 === 0;
    }
}
