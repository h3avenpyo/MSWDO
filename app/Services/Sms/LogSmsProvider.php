<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogSmsProvider implements SmsProviderInterface
{
    /**
     * Send an SMS message by logging it to the system log (Local / Test Driver).
     */
    public function send(string $to, string $message, array $options = []): array
    {
        $refId = 'LOG-' . strtoupper(Str::random(10));

        Log::info("[SMS SIMULATION] Dispatched SMS to: {$to}", [
            'to' => $to,
            'message' => $message,
            'reference_id' => $refId,
            'options' => $options,
            'timestamp' => now()->toDateTimeString(),
        ]);

        return [
            'success' => true,
            'reference_id' => $refId,
            'error' => null,
            'data' => [
                'provider' => 'log',
                'recipient' => $to,
                'status' => 'simulated_success',
            ],
        ];
    }
}
