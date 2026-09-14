<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    /**
     * Send an SMS message.
     *
     * @param string $to Recipient phone number (e.g. 09123456789 or +639123456789)
     * @param string $message The message content
     * @param array $options Additional options (metadata, device_id, sender_name, etc.)
     * @return array Array containing ['success' => bool, 'reference_id' => ?string, 'error' => ?string, 'data' => ?array]
     */
    public function send(string $to, string $message, array $options = []): array;
}
