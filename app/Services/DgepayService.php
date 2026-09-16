<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DgepayService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $apiKey;
    protected string $baseUrl;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->clientId     = (string) config('services.dgepay.client_id');
        $this->clientSecret = (string) config('services.dgepay.client_secret');
        $this->apiKey       = (string) config('services.dgepay.api_key');
        $this->baseUrl      = rtrim((string) (config('services.dgepay.base_url') ?: 'https://api-uat.dgepay.net/dipon/v3'), '/');
        $this->callbackUrl  = (string) (config('services.dgepay.callback_url') ?: route('dgepay.callback'));
    }

    /**
     * Authenticate with DGePay and obtain a JWT access token.
     *
     * @return array{success: bool, access_token?: string, message?: string}
     */
    public function authenticate(): array
    {
        try {
            $basicAuth = base64_encode($this->clientId . ':' . $this->clientSecret);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $basicAuth,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($this->baseUrl . '/payment_gateway/authenticate', [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            $data = $response->json();

            if (!$response->successful() || empty($data['data']['access_token'])) {
                $errorMsg = $data['error'][0] ?? ($data['message'] ?? 'DGePay authentication failed.');
                Log::warning('DGePay Auth Failed', ['status' => $response->status(), 'response' => $data]);
                return ['success' => false, 'message' => $errorMsg];
            }

            return [
                'success'      => true,
                'access_token' => $data['data']['access_token'],
            ];
        } catch (\Throwable $e) {
            Log::error('DGePay Auth Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not connect to DGePay: ' . $e->getMessage()];
        }
    }

    /**
     * Initiate payment with DGePay.
     *
     * @param float  $amount
     * @param string $orderId
     * @param string|null $redirectUrl
     * @param string $description
     * @param string|int|null $userReference
     * @param array  $metadata
     * @return array{success: bool, payment_url?: string, transaction_id?: string, message?: string}
     */
    public function initiatePayment(
        float $amount,
        string $orderId,
        ?string $redirectUrl = null,
        string $description = 'Package Purchase',
        $userReference = null,
        array $metadata = []
    ): array {
        try {
            $auth = $this->authenticate();
            if (!$auth['success']) {
                return $auth;
            }

            $callbackUrl = $redirectUrl ?: $this->callbackUrl;

            $payload = [
                'amount'                => (float) $amount,
                'customer_token'        => null,
                'note'                  => $description,
                'payee_information'     => null,
                'payment_method'        => null,
                'redirect_url'          => $callbackUrl,
                'unique_txn_id'         => $orderId,
                'meta_data'             => [
                    'custom_field_1' => (string) ($metadata['custom_field_1'] ?? 'package_order'),
                    'custom_field_2' => (string) ($metadata['custom_field_2'] ?? ($userReference ? "user_{$userReference}" : '')),
                    'custom_field_3' => (string) ($metadata['custom_field_3'] ?? $orderId),
                ],
                'unique_user_reference' => $userReference ? (string) $userReference : null,
            ];

            $signature     = $this->generateSignature($payload);
            $encryptedBody = $this->encryptPayload($payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $auth['access_token'],
                'Signature'     => $signature,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->withBody($encryptedBody, 'application/json')
              ->post($this->baseUrl . '/payment_gateway/initiate_payment');

            $data = $response->json();

            if (!$response->successful() || empty($data['data']['webview_url'])) {
                $errorMsg = $data['error'][0] ?? ($data['message'] ?? 'Failed to initiate DGePay payment.');
                Log::warning('DGePay Initiate Payment Failed', ['status' => $response->status(), 'response' => $data]);
                return [
                    'success' => false,
                    'message' => $errorMsg,
                ];
            }

            return [
                'success'        => true,
                'payment_url'    => $data['data']['webview_url'],
                'transaction_id' => $data['data']['unique_txn_id'] ?? $orderId,
            ];
        } catch (\Throwable $e) {
            Log::error('DGePay Initiate Payment Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not connect to DGePay: ' . $e->getMessage()];
        }
    }

    /**
     * Check transaction status with DGePay server-to-server.
     *
     * @param string $orderId
     * @return array{success: bool, data?: array, message?: string}
     */
    public function checkTransactionStatus(string $orderId): array
    {
        try {
            $auth = $this->authenticate();
            if (!$auth['success']) {
                return $auth;
            }

            $payload       = ['unique_txn_id' => $orderId];
            $signature     = $this->generateSignature($payload);
            $encryptedBody = $this->encryptPayload($payload);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $auth['access_token'],
                'Signature'     => $signature,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->withBody($encryptedBody, 'application/json')
              ->post($this->baseUrl . '/payment_gateway/check_transaction_status');

            $data = $response->json();

            if (!$response->successful()) {
                Log::warning('DGePay Status Check Failed', ['orderId' => $orderId, 'response' => $data]);
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Failed to verify payment status with DGePay.',
                ];
            }

            return [
                'success' => true,
                'data'    => $data['data'] ?? $data,
            ];
        } catch (\Throwable $e) {
            Log::error('DGePay Check Status Exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to connect for status check: ' . $e->getMessage()];
        }
    }

    /**
     * Decrypt and parse encrypted callback data from query string (?data=...).
     *
     * @param string $encryptedData
     * @return array|null
     */
    public function decryptCallbackData(string $encryptedData): ?array
    {
        try {
            // Fix PHP query string replacing '+' with space
            $cleanData = str_replace(' ', '+', $encryptedData);

            // Attempt 1: Standard base64 decode inside openssl_decrypt
            $decrypted = openssl_decrypt($cleanData, 'AES-128-ECB', $this->clientSecret, 0);

            // Attempt 2: Explicit base64_decode first
            if ($decrypted === false) {
                $decoded = base64_decode($cleanData, true);
                if ($decoded !== false) {
                    $decrypted = openssl_decrypt($decoded, 'AES-128-ECB', $this->clientSecret, OPENSSL_RAW_DATA);
                }
            }

            if ($decrypted === false || empty($decrypted)) {
                Log::warning('DGePay Callback Decryption Failed', ['raw' => substr($encryptedData, 0, 50)]);
                return null;
            }

            $data = json_decode($decrypted, true);
            return is_array($data) ? $data : null;
        } catch (\Throwable $e) {
            Log::error('DGePay Decrypt Callback Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate HMAC-SHA256 signature according to DGePay v1.9 specification.
     */
    public function generateSignature(array $data): string
    {
        $flatString = $this->flattenParams($data);
        $flatString = str_replace(['{', '}', '"', ':', ' ', ','], '', $flatString);

        $hmac = hash_hmac('sha256', $flatString, $this->apiKey, true);
        return base64_encode($hmac);
    }

    /**
     * Recursively flatten params alphabetically for signature generation.
     */
    protected function flattenParams(array $data): string
    {
        ksort($data);

        $result = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result .= $key . $this->flattenParams($value);
            } else {
                if (is_null($value)) {
                    $strValue = 'null';
                } elseif (is_bool($value)) {
                    $strValue = $value ? 'true' : 'false';
                } elseif (is_int($value) || is_float($value)) {
                    $strValue = number_format((float) $value, 1, '.', '');
                } else {
                    $strValue = (string) $value;
                }
                $result .= $key . $strValue;
            }
        }

        return $result;
    }

    /**
     * Encrypt payload using AES-128-ECB.
     */
    public function encryptPayload(array $data): string
    {
        $json = json_encode($data);
        return openssl_encrypt($json, 'AES-128-ECB', $this->clientSecret, 0);
    }
}

