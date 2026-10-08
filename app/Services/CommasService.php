<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin client for the Commas public API (formerly FanBasis).
 * Docs: https://commasdocs.com — auth is a single x-api-key header.
 *
 * Every method throws RuntimeException on transport errors or an
 * API-level {"status":"error"} so callers can fail loudly; the one
 * exception is getTransaction(), which returns null on 404.
 */
class CommasService
{
    private const BASE_URLS = [
        'production' => 'https://www.fanbasis.com',      // must be www — the apex 301s and drops POST bodies
        'sandbox'    => 'https://api-sandbox.commas.net',
    ];

    public const WEBHOOK_EVENTS = [
        'payment.succeeded',
        'payment.failed',
        'product.purchased',
        'refund.created',
        'dispute.created',
        'dispute.updated',
        'subscription.created',
        'subscription.renewed',
        'subscription.canceled',
        'subscription.past_due',
        'subscription.recovered',
    ];

    public function environment(): string
    {
        return config('services.commas.environment') === 'production' ? 'production' : 'sandbox';
    }

    public function isConfigured(): bool
    {
        return (string) config('services.commas.api_key') !== '';
    }

    // ── Checkout ─────────────────────────────────────────────────────────────

    /**
     * Server-side session for the embedded SDK. Metadata (string values only)
     * comes back on webhooks under data.api_metadata.data.
     *
     * @return array{id: ?string, secret: string}
     */
    public function createEmbeddedSession(array $metadata): array
    {
        $json = $this->send('post', '/public-api/checkout-sessions/embedded', [
            'metadata' => array_map('strval', $metadata),
        ]);

        $secret = data_get($json, 'data.checkout_session_secret') ?? data_get($json, 'checkout_session_secret');
        if (! $secret) {
            throw new RuntimeException('Commas embedded session response had no checkout_session_secret');
        }

        return [
            'id'     => (string) (data_get($json, 'data.id') ?? data_get($json, 'id') ?? ''),
            'secret' => (string) $secret,
        ];
    }

    // ── Transactions ─────────────────────────────────────────────────────────

    /** Single transaction (hashid or ORD- id), or null when Commas doesn't know it. */
    public function getTransaction(string $transactionId): ?array
    {
        $response = $this->request()->get('/public-api/transactions/' . rawurlencode($transactionId));

        if ($response->status() === 404) {
            return null;
        }

        return $this->decode($response, 'GET transaction')['data'] ?? null;
    }

    /** One page of all transactions, newest first. */
    public function listTransactions(int $page = 1, int $perPage = 100): array
    {
        $json = $this->send('get', '/public-api/checkout-sessions/transactions', [
            'page'     => $page,
            'per_page' => $perPage,
        ]);

        return [
            'transactions' => data_get($json, 'data.transactions', []),
            'has_more'     => (bool) data_get($json, 'data.pagination.has_more', false),
        ];
    }

    // ── Products ─────────────────────────────────────────────────────────────

    /** Price is in DOLLARS on this endpoint (not cents). */
    public function createProduct(string $title, float $price, string $description = ''): array
    {
        $json = $this->send('post', '/public-api/products/create', [
            'title'       => $title,
            'price'       => $price,
            'type'        => 'onetime',
            'description' => $description,
        ]);

        return $json['data'] ?? [];
    }

    public function listProducts(): array
    {
        $json = $this->send('get', '/public-api/products', ['per_page' => 100]);

        return data_get($json, 'data.data', []);
    }

    // ── Webhook subscriptions ────────────────────────────────────────────────

    public function listWebhookSubscriptions(): array
    {
        return $this->send('get', '/public-api/webhook-subscriptions')['data'] ?? [];
    }

    /** Returns the created subscription, including the one-time secret_key. */
    public function createWebhookSubscription(string $url, array $events = self::WEBHOOK_EVENTS): array
    {
        return $this->send('post', '/public-api/webhook-subscriptions', [
            'webhook_url' => $url,
            'event_types' => array_values($events),
        ])['data'] ?? [];
    }

    // ── Signature ────────────────────────────────────────────────────────────

    /** x-webhook-signature = hex HMAC-SHA256 of the raw body, keyed by the webhook secret. */
    public static function signatureIsValid(string $rawBody, string $signature, string $secret): bool
    {
        if ($secret === '' || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $rawBody, $secret), strtolower(trim($signature)));
    }

    // ── Internals ────────────────────────────────────────────────────────────

    private function request(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('COMMAS_API_KEY is not set');
        }

        return Http::baseUrl(self::BASE_URLS[$this->environment()])
            ->withHeaders(['x-api-key' => (string) config('services.commas.api_key')])
            ->acceptJson()
            ->timeout(20);
    }

    private function send(string $method, string $path, array $data = []): array
    {
        $response = $method === 'get'
            ? $this->request()->get($path, $data)
            : $this->request()->{$method}($path, $data);

        return $this->decode($response, strtoupper($method) . ' ' . $path);
    }

    private function decode(Response $response, string $label): array
    {
        $json = json_decode(ltrim($response->body(), "\xEF\xBB\xBF"), true);

        if (! $response->successful() || ! is_array($json) || ($json['status'] ?? null) === 'error') {
            Log::error('[Commas] API call failed', [
                'call'       => $label,
                'status'     => $response->status(),
                'message'    => is_array($json) ? ($json['message'] ?? null) : null,
                'errors'     => is_array($json) ? ($json['errors'] ?? null) : null,
                'request_id' => is_array($json) ? ($json['request_id'] ?? null) : null,
            ]);

            throw new RuntimeException(sprintf(
                'Commas %s failed (HTTP %d): %s',
                $label,
                $response->status(),
                is_array($json) ? ($json['message'] ?? 'unknown error') : 'non-JSON response'
            ));
        }

        return $json;
    }
}
