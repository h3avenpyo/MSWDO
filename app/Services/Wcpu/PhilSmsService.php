<?php

namespace App\Services\Wcpu;

use App\Models\Wcpu\CaseReferral;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhilSmsService
{
    protected $apiToken;
    protected $senderId;
    protected $baseUrl = 'https://dashboard.philsms.com/api/v3/sms/send';

    public function __construct()
    {
        $this->apiToken = config('services.philsms.token');
        $this->senderId = config('services.philsms.sender_id', 'WCPU');
    }

    /**
     * Send an SMS message
     *
     * @param string $recipient Phone number (e.g., 09171234567 or +639171234567)
     * @param string $message The message content
     * @return array ['success' => bool, 'message' => string, 'response' => mixed]
     */
    public function send(string $recipient, string $message): array
    {
        if (empty($this->apiToken)) {
            Log::warning('PhilSMS API token not configured');
            return [
                'success' => false,
                'message' => 'SMS service not configured',
                'response' => null
            ];
        }

        // Format phone number (ensure it starts with country code 63, no +)
        $originalNumber = $recipient;
        $recipient = $this->formatPhoneNumber($recipient);
        
        Log::info('PhilSMS formatting number', [
            'original' => $originalNumber,
            'formatted' => $recipient
        ]);

        if (empty($recipient)) {
            return [
                'success' => false,
                'message' => 'Invalid phone number',
                'response' => null
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl, [
                'recipient' => $recipient,
                'sender_id' => $this->senderId,
                'type' => 'plain',
                'message' => $message,
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
                Log::info('SMS sent successfully', [
                    'recipient' => $recipient,
                    'response' => $responseData
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => $responseData
                ];
            }

            Log::error('PhilSMS API error', [
                'recipient' => $recipient,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Failed to send SMS',
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('PhilSMS exception', [
                'recipient' => $recipient,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Send referral notification SMS to victim or guardian
     *
     * @param CaseReferral $referral
     * @return array ['success' => bool, 'message' => string, 'sent_to' => string|null]
     */
    public function sendVictimReferralNotification($referral): array
    {
        $clientAge = (int) $referral->client_age;
        $hasGuardian = !empty($referral->guardian_name) && !empty($referral->guardian_contact);
        
        // Determine who to send SMS to
        if ($clientAge >= 18 && !$hasGuardian) {
            // Adult victim without guardian - send to victim
            $phoneNumber = $referral->client_contact;
            $recipientType = 'victim';
            $recipientName = $referral->client_name;
        } else {
            // Minor or has guardian - send to guardian, fallback to victim if no guardian contact
            $phoneNumber = $referral->guardian_contact ?: $referral->client_contact;
            $recipientType = $referral->guardian_contact ? 'guardian' : 'victim';
            $recipientName = $referral->guardian_contact ? ($referral->guardian_name ?? 'Guardian') : $referral->client_name;
        }
        
        if (empty($phoneNumber)) {
            return [
                'success' => false,
                'message' => "No contact number available for {$recipientType}",
                'sent_to' => null
            ];
        }
        
        $message = $this->buildVictimReferralMessage($referral, $recipientType, $recipientName);
        $result = $this->send($phoneNumber, $message);
        $result['sent_to'] = $recipientType;
        $result['sent_to_name'] = $recipientName;
        $result['sent_to_number'] = $phoneNumber;
        
        return $result;
    }

    /**
     * Build the referral notification message for victim/guardian
     *
     * @param CaseReferral $referral
     * @param string $recipientType 'victim' or 'guardian'
     * @param string $recipientName
     * @return string
     */
    protected function buildVictimReferralMessage($referral, string $recipientType, string $recipientName): string
    {
        $dateStr = $referral->date_of_referral 
            ? (is_string($referral->date_of_referral) 
                ? $referral->date_of_referral 
                : (is_object($referral->date_of_referral) && method_exists($referral->date_of_referral, 'format') 
                    ? $referral->date_of_referral->format('M d, Y') 
                    : (string)$referral->date_of_referral))
            : 'N/A';
        
        if ($recipientType === 'guardian') {
            return "WCPU Notice\n" .
                   "Dear {$recipientName},\n" .
                   "The case of {$referral->client_name} (Ref#: {$referral->referral_number}) has been referred to {$referral->recipient_name}" .
                   (!empty($referral->recipient_role) ? " ({$referral->recipient_role})" : "") .
                   " on {$dateStr}.\n" .
                   "For inquiries, contact WCPU Silang.";
        }
        
        // For victim
        return "WCPU Notice\n" .
               "Dear {$recipientName},\n" .
               "Your case (Ref#: {$referral->referral_number}) has been referred to {$referral->recipient_name}" .
               (!empty($referral->recipient_role) ? " ({$referral->recipient_role})" : "") .
               " on {$dateStr}.\n" .
               "For inquiries, contact WCPU Silang.";
    }

    /**
     * Send referral notification SMS (legacy - for report recipient)
     *
     * @param CaseReferral $referral
     * @param string $phoneNumber
     * @return array
     * @deprecated Use sendVictimReferralNotification instead
     */
    public function sendReferralNotification($referral, string $phoneNumber): array
    {
        $message = $this->buildReferralMessage($referral);
        return $this->send($phoneNumber, $message);
    }

    /**
     * Build the referral notification message (legacy)
     *
     * @param CaseReferral $referral
     * @return string
     * @deprecated
     */
    protected function buildReferralMessage($referral): string
    {
        $dateStr = $referral->date_of_referral 
            ? (is_string($referral->date_of_referral) 
                ? $referral->date_of_referral 
                : (is_object($referral->date_of_referral) && method_exists($referral->date_of_referral, 'format') 
                    ? $referral->date_of_referral->format('M d, Y') 
                    : (string)$referral->date_of_referral))
            : 'N/A';
            
        return "WCPU Case Referral\n" .
               "Ref#: {$referral->referral_number}\n" .
               "Date: " . $dateStr . "\n" .
               "Client: {$referral->client_name}\n" .
               "Reason: " . \Illuminate\Support\Str::limit($referral->reason_for_referral, 50) . "\n" .
               "Please check your email for the complete referral document.";
    }

    /**
     * Format phone number to Philippine format
     *
     * @param string $phone
     * @return string|null
     */
    protected function formatPhoneNumber(string $phone): ?string
    {
        // Remove all non-numeric characters (including +)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) {
            return null;
        }

        // If starts with 63 and is 12 digits, it's already correct
        if (str_starts_with($phone, '63') && strlen($phone) === 12) {
            return $phone;
        }

        // If starts with 09, convert to 639
        if (str_starts_with($phone, '09') && strlen($phone) === 11) {
            return '63' . substr($phone, 1);
        }

        // If starts with 9 and is 10 digits, add 63
        if (str_starts_with($phone, '9') && strlen($phone) === 10) {
            return '63' . $phone;
        }

        return null;
    }
}
