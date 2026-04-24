<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthorizeNetService
{
    private string $endpoint;
    private string $apiLoginId;
    private string $transactionKey;

    public function __construct()
    {
        $this->apiLoginId     = (string) config('services.authorize_net.api_login_id', '');
        $this->transactionKey = (string) config('services.authorize_net.transaction_key', '');

        $this->endpoint = config('services.authorize_net.environment') === 'production'
            ? 'https://api.authorize.net/xml/v1/request.api'
            : 'https://apitest.authorize.net/xml/v1/request.api';
    }

    public function getAllSubscriptions(): array
    {
        $all = [];
        $limit = 1000;

        foreach (['subscriptionActive', 'subscriptionInactive', 'subscriptionExpired'] as $type) {
            $offset = 1;
            do {
                $resp = Http::timeout(30)->post($this->endpoint, [
                    'ARBGetSubscriptionListRequest' => [
                        'merchantAuthentication' => [
                            'name'           => $this->apiLoginId,
                            'transactionKey' => $this->transactionKey,
                        ],
                        'searchType' => $type,
                        'sorting'    => ['orderBy' => 'id', 'orderDescending' => 'false'],
                        'paging'     => ['limit' => $limit, 'offset' => $offset],
                    ],
                ]);

                $body = ltrim($resp->body(), "\xEF\xBB\xBF");
                $data = json_decode($body, true);

                if (! isset($data['messages']['resultCode']) || $data['messages']['resultCode'] !== 'Ok') {
                    Log::warning('ARBGetSubscriptionList failed', [
                        'type' => $type, 'offset' => $offset, 'response' => $data,
                    ]);
                    break;
                }

                $batch = $data['subscriptionDetails'] ?? [];
                foreach ($batch as $sub) {
                    $all[$sub['id']] = $sub;
                }
                $offset += $limit;
            } while (count($batch) >= $limit);
        }

        return array_values($all);
    }
}