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
     * Clean and format a Philippine contact number.
     * Returns sanitized number in canonical format e.g. 09171234567 or +639171234567.
     * Returns null if invalid or cannot be parsed.
     */
    public static function normalizeContactNumber(?string $rawNumber): ?string
    {
        if (empty($rawNumber)) {
            return null;
        }

        // Strip non-digit characters except leading plus
        $hasPlus = str_starts_with(trim($rawNumber), '+');
        $digits = preg_replace('/[^\d]/', '', $rawNumber);

        if (empty($digits)) {
            return null;
        }

        // Standard Philippine mobile numbers
        if (str_starts_with($digits, '639') && strlen($digits) === 12) {
            return '0' . substr($digits, 2); // Standardize to 09XXXXXXXXX
        }

        if (str_starts_with($digits, '9') && strlen($digits) === 10) {
            return '0' . $digits; // Standardize 9XXXXXXXXX to 09XXXXXXXXX
        }

        if (str_starts_with($digits, '09') && strlen($digits) === 11) {
            return $digits;
        }

        // International with plus
        if ($hasPlus && strlen($digits) >= 10 && strlen($digits) <= 15) {
            return '+' . $digits;
        }

        // If it's 11 digits
        if (strlen($digits) === 11) {
            return $digits;
        }

        return null;
    }

    /**
     * Validate if contact number is acceptable.
     */
    public static function isValidContactNumber(?string $rawNumber): bool
    {
        return !is_null(static::normalizeContactNumber($rawNumber));
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
        $normalizedTo = static::normalizeContactNumber($to);

        if (!$normalizedTo) {
            return [
                'success' => false,
                'reference_id' => null,
                'error' => 'Invalid contact number format. Please provide a valid 11-digit mobile number (e.g. 09171234567).',
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
}
