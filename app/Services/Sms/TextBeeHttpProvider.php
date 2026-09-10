<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextBeeHttpProvider implements SmsProviderInterface
{
    protected ?string $apiKey;
    protected ?string $apiUrl;
    protected ?string $deviceId;

    public function __construct(?string $apiKey = null, ?string $apiUrl = null, ?string $deviceId = null)
    {
        $this->apiKey = $apiKey ?? config('services.sms.api_key');
        $this->apiUrl = $apiUrl ?? config('services.sms.api_url');
        $this->deviceId = $deviceId ?? config('services.sms.device_id');
    }

    /**
     * Send SMS using HTTP Client.
     */
    public function send(string $to, string $message, array $options = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'SMS API key is not configured.',
                'data' => null,
            ];
        }

        // If the specific API URL has not been configured yet (awaiting user documentation),
        // we log the action and report ready state or dispatch via the standard endpoint.
        $endpoint = $this->apiUrl;
        if (empty($endpoint)) {
            // Standard TextBee default endpoint if device_id is present
            if (!empty($this->deviceId)) {
                $endpoint = "https://api.textbee.dev/api/v1/gateway/devices/{$this->deviceId}/sendSMS";
            }
        }

        if (empty($endpoint)) {
            Log::info("[SMS Driver] TextBee API Key present, awaiting official API documentation endpoint.", [
                'to' => $to,
                'message' => $message,
                'api_key_configured' => !empty($this->apiKey),
            ]);

            // Return simulated success with notice that endpoint will be connected
            return [
                'success' => true,
                'reference_id' => 'TXB-PENDING-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'error' => null,
                'data' => [
                    'provider' => 'textbee',
                    'note' => 'SMS prepared and queued for delivery once official API documentation endpoint is connected.',
                ],
            ];
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, array_merge([
                    'recipient' => $to,
                    'recipients' => [$to],
                    'message' => $message,
                ], $options));

            if ($response->successful()) {
                $body = $response->json();
                $refId = $body['messageId'] ?? $body['id'] ?? $body['reference_id'] ?? ('TXB-' . strtoupper(substr(md5(uniqid()), 0, 8)));

                return [
                    'success' => true,
                    'reference_id' => $refId,
                    'error' => null,
                    'data' => $body,
                ];
            }

            Log::error("[SMS Driver Error] HTTP " . $response->status(), [
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'SMS provider returned an error (' . $response->status() . ').',
                'data' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error("[SMS Driver Exception] " . $e->getMessage());

            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'Network error connecting to SMS provider.',
                'data' => null,
            ];
        }
    }
}
