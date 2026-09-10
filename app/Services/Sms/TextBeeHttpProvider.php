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
     * Send SMS using TextBee HTTP Gateway API.
     */
    public function send(string $to, string $message, array $options = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'SMS API key is not configured. Please set SMS_API_KEY in your .env file.',
                'data' => null,
            ];
        }

        // Format recipient to Philippine international E.164 format (+63XXXXXXXXXX)
        $recipient = SmsService::formatToPhilippineInternational($to) ?? $to;

        // Determine standard TextBee send-sms endpoint
        $endpoint = $this->apiUrl;
        if (empty($endpoint)) {
            $endpoint = 'https://api.textbee.dev/api/v1/gateway/send-sms';
        }

        // Resolve device ID: prioritize explicitly passed options, then config, then auto-discover
        $activeDeviceId = $options['deviceId'] ?? $options['device_id'] ?? $this->deviceId;
        if (empty($activeDeviceId)) {
            $deviceInfo = $this->getPrimaryDevice();
            if ($deviceInfo && !empty($deviceInfo['id'])) {
                $activeDeviceId = $deviceInfo['id'];

                if (isset($deviceInfo['status']) && strtoupper($deviceInfo['status']) === 'OFFLINE') {
                    Log::warning("[SMS Gateway] Primary TextBee device is reported as OFFLINE.", [
                        'device_id' => $activeDeviceId,
                        'device_name' => $deviceInfo['name'] ?? 'Unknown',
                    ]);
                }
            }
        }

        // Build TextBee payload: recipients is an array of E.164 formatted numbers
        $payload = [
            'recipients' => [$recipient],
            'message' => $message,
        ];

        // Include deviceId if specified or discovered
        if (!empty($activeDeviceId)) {
            $payload['deviceId'] = $activeDeviceId;
        }

        // Merge any optional parameters (e.g. simSubscriptionId)
        if (!empty($options['simSubscriptionId'])) {
            $payload['simSubscriptionId'] = $options['simSubscriptionId'];
        }

        try {
            Log::info("[SMS Gateway Request] Dispatching SMS to {$recipient}", [
                'endpoint' => $endpoint,
                'device_id' => $activeDeviceId,
                'recipient' => $recipient,
                'message_preview' => mb_substr($message, 0, 50) . '...',
            ]);

            $response = Http::timeout(20)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($endpoint, $payload);

            $body = $response->json() ?? [];

            Log::info("[SMS Gateway Response] HTTP " . $response->status(), [
                'status' => $response->status(),
                'body' => $body,
            ]);

            // Determine if the SMS API genuinely succeeded
            $isSuccess = $response->successful() && (
                !isset($body['success']) || $body['success'] === true
            ) && (
                !isset($body['data']['success']) || $body['data']['success'] === true
            );

            if ($isSuccess) {
                // Extract real reference / batch ID from TextBee response
                $refId = $body['data']['smsBatchId'] 
                    ?? $body['smsBatchId'] 
                    ?? $body['data']['id'] 
                    ?? $body['messageId'] 
                    ?? $body['id'] 
                    ?? ('TXB-' . strtoupper(substr(md5(uniqid()), 0, 8)));

                return [
                    'success' => true,
                    'reference_id' => $refId,
                    'error' => null,
                    'data' => $body,
                ];
            }

            // Extract exact error detail from response
            $errorMsg = $body['message'] 
                ?? $body['error'] 
                ?? $body['data']['message'] 
                ?? null;

            if (is_array($errorMsg)) {
                $errorMsg = implode(', ', $errorMsg);
            }

            if (empty($errorMsg)) {
                $rawBody = $response->body();
                $errorMsg = (!empty($rawBody) && strlen($rawBody) < 200) 
                    ? $rawBody 
                    : ('SMS provider returned HTTP error ' . $response->status());
            }

            // Helpful explanations for common gateway errors
            if ($response->status() === 401) {
                $errorMsg = 'Invalid SMS API key (HTTP 401). Please verify SMS_API_KEY in your .env file.';
            } elseif ($response->status() === 404 && empty($activeDeviceId)) {
                $errorMsg = 'No active SMS device found. Please ensure your Android phone is connected to the TextBee app.';
            }

            Log::error("[SMS Gateway Delivery Error] HTTP " . $response->status(), [
                'endpoint' => $endpoint,
                'recipient' => $recipient,
                'error' => $errorMsg,
                'response' => $body,
            ]);

            return [
                'success' => false,
                'reference_id' => null,
                'error' => $errorMsg,
                'data' => $body,
            ];

        } catch (\Throwable $e) {
            Log::error("[SMS Gateway Connection Exception] " . $e->getMessage(), [
                'endpoint' => $endpoint,
                'recipient' => $recipient,
            ]);

            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'Connection error to SMS gateway: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Auto-discover primary active device registered under this TextBee account.
     */
    public function getPrimaryDevice(): ?array
    {
        if (empty($this->apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get('https://api.textbee.dev/api/v1/gateway/devices');

            if ($response->successful()) {
                $body = $response->json();
                $devices = $body['data'] ?? $body['devices'] ?? (is_array($body) && isset($body[0]) ? $body : []);
                if (!empty($devices) && is_array($devices)) {
                    // Prioritize ONLINE device
                    foreach ($devices as $d) {
                        if (isset($d['status']) && strtoupper($d['status']) === 'ONLINE') {
                            return [
                                'id' => $d['id'] ?? $d['_id'] ?? null,
                                'name' => $d['name'] ?? $d['brand'] ?? 'Android Device',
                                'status' => 'ONLINE',
                            ];
                        }
                    }

                    // Fallback to first registered device
                    $first = $devices[0];
                    return [
                        'id' => $first['id'] ?? $first['_id'] ?? null,
                        'name' => $first['name'] ?? $first['brand'] ?? 'Android Device',
                        'status' => $first['status'] ?? 'UNKNOWN',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning("[SMS Gateway] Failed to query TextBee devices: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Check device status and connectivity for diagnostic purposes.
     */
    public function checkGatewayStatus(): array
    {
        if (empty($this->apiKey)) {
            return [
                'configured' => false,
                'status' => 'unconfigured',
                'message' => 'SMS API key is not configured in .env.',
                'devices' => [],
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get('https://api.textbee.dev/api/v1/gateway/devices');

            if ($response->successful()) {
                $body = $response->json();
                $devices = $body['data'] ?? $body['devices'] ?? (is_array($body) && isset($body[0]) ? $body : []);

                $hasOnline = false;
                $deviceList = [];
                if (is_array($devices)) {
                    foreach ($devices as $d) {
                        $isOnline = isset($d['status']) && strtoupper($d['status']) === 'ONLINE';
                        if ($isOnline) $hasOnline = true;
                        $deviceList[] = [
                            'id' => $d['id'] ?? $d['_id'] ?? 'N/A',
                            'name' => $d['name'] ?? $d['brand'] ?? 'Android Device',
                            'status' => $d['status'] ?? 'UNKNOWN',
                            'battery' => $d['battery'] ?? null,
                        ];
                    }
                }

                return [
                    'configured' => true,
                    'status' => count($deviceList) > 0 ? ($hasOnline ? 'online' : 'offline') : 'no_devices',
                    'device_count' => count($deviceList),
                    'has_online_device' => $hasOnline,
                    'devices' => $deviceList,
                    'message' => count($deviceList) > 0
                        ? ($hasOnline ? 'SMS Gateway device is ONLINE and ready.' : 'SMS Gateway device is registered but currently OFFLINE.')
                        : 'No SMS device registered. Please connect your Android phone in the TextBee app.',
                ];
            }

            return [
                'configured' => true,
                'status' => 'error',
                'message' => $response->status() === 401 ? 'Invalid TextBee API key (HTTP 401).' : ('Gateway returned HTTP ' . $response->status()),
                'devices' => [],
            ];
        } catch (\Throwable $e) {
            return [
                'configured' => true,
                'status' => 'network_error',
                'message' => 'Cannot reach TextBee gateway: ' . $e->getMessage(),
                'devices' => [],
            ];
        }
    }
}
