<?php

namespace App\Services\Sms;

use InvalidArgumentException;

class SmsService
{
    protected SmsProviderInterface $provider;

    public function __construct(?SmsProviderInterface $provider = null)
    {
        if ($provider) {
            $this->provider = $provider;
        } else {
            $driver = config('services.sms.default', 'textbee');
            $this->provider = match ($driver) {
                'log' => new LogSmsProvider(),
                default => new TextBeeHttpProvider(),
            };
        }
    }

    /**
     * Set a custom provider at runtime.
     */
    public function setProvider(SmsProviderInterface $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Clean and format a Philippine contact number into the international format (+63XXXXXXXXXX).
     * Accepts:
     * - 09XXXXXXXXX (11 digits local format)
     * - +639XXXXXXXXX (13 chars international format with plus)
     * - 639XXXXXXXXX (12 digits international format without plus)
     * - 9XXXXXXXXX (10 digits without leading zero)
     * - Formats with hyphens, spaces, parentheses: e.g. +63 917-123-4567
     * - Accidental +6309XXXXXXXXX format
     *
     * Returns string in canonical international format (+63XXXXXXXXXX, e.g. +639171234567).
     * Returns null if invalid or cannot be parsed as a valid mobile number.
     */
    public static function formatToPhilippineInternational(?string $rawNumber): ?string
    {
        if (empty($rawNumber)) {
            return null;
        }

        $trimmed = trim($rawNumber);
        if ($trimmed === 'N/A' || $trimmed === 'No contact' || strtolower($trimmed) === 'none') {
            return null;
        }

        $hasPlus = str_starts_with($trimmed, '+');
        $digits = preg_replace('/[^\d]/', '', $trimmed);

        if (empty($digits)) {
            return null;
        }

        // Case 1: 12 digits starting with 639 or 6389 (e.g. 639171234567 or +639171234567)
        if (strlen($digits) === 12 && (str_starts_with($digits, '639') || str_starts_with($digits, '6389'))) {
            return '+' . $digits;
        }

        // Case 2: 13 digits starting with 6309 or 63089 (accidental leading 0 after 63)
        if (strlen($digits) === 13 && (str_starts_with($digits, '6309') || str_starts_with($digits, '63089'))) {
            return '+63' . substr($digits, 3);
        }

        // Case 3: 11 digits starting with 09 or 089 (local format e.g. 09171234567)
        if (strlen($digits) === 11 && (str_starts_with($digits, '09') || str_starts_with($digits, '089'))) {
            return '+63' . substr($digits, 1);
        }

        // Case 4: 10 digits starting with 9 or 89 (e.g. 9171234567)
        if (strlen($digits) === 10 && (str_starts_with($digits, '9') || str_starts_with($digits, '89'))) {
            return '+63' . $digits;
        }

        // International format with other country codes (+ followed by 10-15 digits)
        if ($hasPlus && strlen($digits) >= 10 && strlen($digits) <= 15) {
            return '+' . $digits;
        }

        return null;
    }

    /**
     * Clean and format a Philippine contact number into the local format (09XXXXXXXXX).
     * Returns null if invalid.
     */
    public static function formatToLocal(?string $rawNumber): ?string
    {
        $international = static::formatToPhilippineInternational($rawNumber);
        if (!$international) {
            return null;
        }

        if (str_starts_with($international, '+63') && strlen($international) === 13) {
            return '0' . substr($international, 3);
        }

        return $international;
    }

    /**
     * Clean and normalize a contact number into the required international format (+63XXXXXXXXXX).
     * Backward-compatible alias for formatToPhilippineInternational.
     */
    public static function normalizeContactNumber(?string $rawNumber): ?string
    {
        return static::formatToPhilippineInternational($rawNumber);
    }

    /**
     * Validate if contact number is an acceptable mobile number.
     */
    public static function isValidContactNumber(?string $rawNumber): bool
    {
        return !is_null(static::formatToPhilippineInternational($rawNumber));
    }

    /**
     * Generate the official unclaimed financial assistance notification text in Tagalog.
     */
    public static function generateUnclaimedMessage(string $beneficiaryName, ?string $formattedClaimingDate): string
    {
        $claimingDateStr = !empty($formattedClaimingDate) ? $formattedClaimingDate : '[Claiming Date]';

        return "Magandang araw, {$beneficiaryName}. Ito po ay mula sa Municipal Social Welfare and Development Office (MSWDO). Ang inyong tulong pinansyal ay maaari nang kunin sa {$claimingDateStr}. Mangyaring magtungo sa aming tanggapan sa naturang petsa at dalhin ang inyong valid ID at mga kinakailangang dokumento. Maraming salamat po.";
    }

    /**
     * Send SMS to a recipient.
     *
     * @param string $to Raw or normalized contact number
     * @param string $message Text message
     * @param array $options Additional options
     * @return array
     */
    public function send(string $to, string $message, array $options = []): array
    {
        $normalizedTo = static::formatToPhilippineInternational($to);

        if (!$normalizedTo) {
            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'Invalid Philippine mobile number format. Please provide a valid mobile number (e.g. 09XXXXXXXXX or +639XXXXXXXXX).',
                'data' => null,
            ];
        }

        $trimmedMessage = trim($message);
        if (empty($trimmedMessage)) {
            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'Message content cannot be empty.',
                'data' => null,
            ];
        }

        return $this->provider->send($normalizedTo, $trimmedMessage, $options);
    }

    /**
     * Check gateway and device status.
     */
    public function checkGatewayStatus(): array
    {
        if (method_exists($this->provider, 'checkGatewayStatus')) {
            return $this->provider->checkGatewayStatus();
        }

        return [
            'configured' => true,
            'status' => 'ready',
            'message' => 'Provider is active.',
            'devices' => [],
        ];
    }
}
