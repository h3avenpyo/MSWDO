<?php

namespace App\Services\Financial;

use App\Models\Financial\FinancialAssistanceMessage;
use App\Models\Financial\OnlineFinancialIntake;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Sms\SmsService;
use Illuminate\Support\Facades\Log;

class OnlineFinancialNotificationService
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send automatic Tagalog approval SMS to the applicant.
     * Guaranteed idempotent: will not send duplicate messages if already sent.
     */
    public function notifyApproval(
        OnlineFinancialIntake $onlineIntake,
        ?BeneficiaryIntake $beneficiaryIntake = null,
        ?string $officer = null
    ): array {
        // Enforce that application must be in Accepted status
        if ($onlineIntake->status !== 'Accepted') {
            Log::info("[OnlineFinancialSMS] Skipped approval SMS: Intake #{$onlineIntake->id} is not Accepted (Current: {$onlineIntake->status})");
            return [
                'success' => false,
                'skipped' => true,
                'message' => 'Cannot send approval SMS: Application status is not Accepted.',
            ];
        }

        // Idempotency check: prevent duplicate approval SMS
        if ($this->hasAlreadySent($onlineIntake->id, 'Online Application Approved')) {
            Log::info("[OnlineFinancialSMS] Duplicate approval SMS prevented for Intake #{$onlineIntake->id}");
            return [
                'success' => true,
                'already_sent' => true,
                'message' => 'Ang abiso ng pag-apruba ay naipadala na sa aplikante.',
            ];
        }

        $recipientName = $onlineIntake->applicant_name;
        $rawContact = $onlineIntake->applicant_contact_number;
        $officerName = $officer ?? session('admin_user_name') ?? 'MSWDO Staff';
        $messageText = SmsService::generateOnlineApprovedMessage($recipientName);
        $intakeId = $beneficiaryIntake?->id ?? $onlineIntake->beneficiary_intake_id;
        $clientId = $beneficiaryIntake?->client_id;

        // Check for missing contact number
        if (empty($rawContact) || trim($rawContact) === 'N/A' || trim($rawContact) === 'No contact') {
            Log::warning("[OnlineFinancialSMS] No contact number for Intake #{$onlineIntake->id}");
            $this->recordMessage([
                'online_intake_id' => $onlineIntake->id,
                'intake_id' => $intakeId,
                'client_id' => $clientId,
                'beneficiary_name' => $onlineIntake->beneficiary_full_name,
                'recipient_name' => $recipientName,
                'recipient_contact_number' => 'N/A',
                'message_body' => $messageText,
                'message_type' => 'Online Application Approved',
                'status' => 'Failed',
                'error_message' => 'Walang numero ng kontak na nakatala sa aplikasyon.',
                'sent_by' => $officerName,
                'sent_at' => null,
            ]);

            $onlineIntake->update([
                'sms_status' => 'No Number',
                'sms_error' => 'Walang numero ng kontak na nakatala.',
            ]);

            return [
                'success' => false,
                'error' => 'Walang numero ng kontak ang aplikante.',
            ];
        }

        // Validate and normalize Philippine phone number format
        $normalizedPhone = SmsService::formatToPhilippineInternational($rawContact);
        if (!$normalizedPhone) {
            Log::warning("[OnlineFinancialSMS] Invalid phone format '{$rawContact}' for Intake #{$onlineIntake->id}");
            $this->recordMessage([
                'online_intake_id' => $onlineIntake->id,
                'intake_id' => $intakeId,
                'client_id' => $clientId,
                'beneficiary_name' => $onlineIntake->beneficiary_full_name,
                'recipient_name' => $recipientName,
                'recipient_contact_number' => $rawContact,
                'message_body' => $messageText,
                'message_type' => 'Online Application Approved',
                'status' => 'Failed',
                'error_message' => 'Hindi wastong format ng numero sa Pilipinas.',
                'sent_by' => $officerName,
                'sent_at' => null,
            ]);

            $onlineIntake->update([
                'sms_status' => 'Failed',
                'sms_error' => 'Hindi wastong format ng numero ng telepono.',
            ]);

            return [
                'success' => false,
                'error' => 'Hindi wastong format ng mobile number sa Pilipinas.',
            ];
        }

        // Dispatch SMS via existing SmsService
        try {
            $smsResult = $this->smsService->send($normalizedPhone, $messageText);
        } catch (\Throwable $e) {
            Log::error("[OnlineFinancialSMS] Exception dispatching SMS for Intake #{$onlineIntake->id}: " . $e->getMessage());
            $smsResult = [
                'success' => false,
                'reference_id' => null,
                'error' => 'SMS Gateway exception: ' . $e->getMessage(),
            ];
        }

        // Record message in history
        $messageRecord = $this->recordMessage([
            'online_intake_id' => $onlineIntake->id,
            'intake_id' => $intakeId,
            'client_id' => $clientId,
            'beneficiary_name' => $onlineIntake->beneficiary_full_name,
            'recipient_name' => $recipientName,
            'recipient_contact_number' => $normalizedPhone,
            'message_body' => $messageText,
            'message_type' => 'Online Application Approved',
            'status' => $smsResult['success'] ? 'Sent' : 'Failed',
            'reference_id' => $smsResult['reference_id'] ?? null,
            'error_message' => $smsResult['error'] ?? null,
            'sent_by' => $officerName,
            'sent_at' => $smsResult['success'] ? now() : null,
        ]);

        // Update online intake SMS tracking fields
        if ($smsResult['success']) {
            $onlineIntake->update([
                'sms_status' => 'Sent',
                'sms_error' => null,
                'sms_notified_at' => now(),
                'approval_sms_sent_at' => now(),
            ]);

            Log::info("[OnlineFinancialSMS] Approval SMS successfully sent to {$normalizedPhone} for Intake #{$onlineIntake->id}");

            return [
                'success' => true,
                'message_id' => $messageRecord->id,
                'message' => 'Matagumpay na naipadala ang SMS sa aplikante.',
                'phone' => $normalizedPhone,
            ];
        }

        $onlineIntake->update([
            'sms_status' => 'Failed',
            'sms_error' => $smsResult['error'] ?? 'SMS provider delivery error.',
        ]);

        Log::warning("[OnlineFinancialSMS] Approval SMS failed for Intake #{$onlineIntake->id}: " . ($smsResult['error'] ?? 'Unknown error'));

        return [
            'success' => false,
            'message_id' => $messageRecord->id,
            'error' => $smsResult['error'] ?? 'Nabigo ang pagpapadala ng SMS.',
            'phone' => $normalizedPhone,
        ];
    }

    /**
     * Send automatic Tagalog rejection SMS to the applicant.
     * Guaranteed idempotent: will not send duplicate messages if already sent.
     */
    public function notifyRejection(OnlineFinancialIntake $onlineIntake, ?string $officer = null): array
    {
        // Enforce that application must be in Rejected status
        if ($onlineIntake->status !== 'Rejected') {
            Log::info("[OnlineFinancialSMS] Skipped rejection SMS: Intake #{$onlineIntake->id} is not Rejected (Current: {$onlineIntake->status})");
            return [
                'success' => false,
                'skipped' => true,
                'message' => 'Cannot send rejection SMS: Application status is not Rejected.',
            ];
        }

        // Idempotency check: prevent duplicate rejection SMS
        if ($this->hasAlreadySent($onlineIntake->id, 'Online Application Rejected')) {
            Log::info("[OnlineFinancialSMS] Duplicate rejection SMS prevented for Intake #{$onlineIntake->id}");
            return [
                'success' => true,
                'already_sent' => true,
                'message' => 'Ang abiso ng pagtanggi ay naipadala na sa aplikante.',
            ];
        }

        $recipientName = $onlineIntake->applicant_name;
        $rawContact = $onlineIntake->applicant_contact_number;
        $officerName = $officer ?? session('admin_user_name') ?? 'MSWDO Staff';
        $messageText = SmsService::generateOnlineRejectedMessage($recipientName);

        // Check for missing contact number
        if (empty($rawContact) || trim($rawContact) === 'N/A' || trim($rawContact) === 'No contact') {
            Log::warning("[OnlineFinancialSMS] No contact number for Intake #{$onlineIntake->id}");
            $this->recordMessage([
                'online_intake_id' => $onlineIntake->id,
                'intake_id' => null,
                'client_id' => null,
                'beneficiary_name' => $onlineIntake->beneficiary_full_name,
                'recipient_name' => $recipientName,
                'recipient_contact_number' => 'N/A',
                'message_body' => $messageText,
                'message_type' => 'Online Application Rejected',
                'status' => 'Failed',
                'error_message' => 'Walang numero ng kontak na nakatala sa aplikasyon.',
                'sent_by' => $officerName,
                'sent_at' => null,
            ]);

            $onlineIntake->update([
                'sms_status' => 'No Number',
                'sms_error' => 'Walang numero ng kontak na nakatala.',
            ]);

            return [
                'success' => false,
                'error' => 'Walang numero ng kontak ang aplikante.',
            ];
        }

        // Validate and normalize Philippine phone number format
        $normalizedPhone = SmsService::formatToPhilippineInternational($rawContact);
        if (!$normalizedPhone) {
            Log::warning("[OnlineFinancialSMS] Invalid phone format '{$rawContact}' for Intake #{$onlineIntake->id}");
            $this->recordMessage([
                'online_intake_id' => $onlineIntake->id,
                'intake_id' => null,
                'client_id' => null,
                'beneficiary_name' => $onlineIntake->beneficiary_full_name,
                'recipient_name' => $recipientName,
                'recipient_contact_number' => $rawContact,
                'message_body' => $messageText,
                'message_type' => 'Online Application Rejected',
                'status' => 'Failed',
                'error_message' => 'Hindi wastong format ng numero sa Pilipinas.',
                'sent_by' => $officerName,
                'sent_at' => null,
            ]);

            $onlineIntake->update([
                'sms_status' => 'Failed',
                'sms_error' => 'Hindi wastong format ng numero ng telepono.',
            ]);

            return [
                'success' => false,
                'error' => 'Hindi wastong format ng mobile number sa Pilipinas.',
            ];
        }

        // Dispatch SMS via existing SmsService
        try {
            $smsResult = $this->smsService->send($normalizedPhone, $messageText);
        } catch (\Throwable $e) {
            Log::error("[OnlineFinancialSMS] Exception dispatching rejection SMS for Intake #{$onlineIntake->id}: " . $e->getMessage());
            $smsResult = [
                'success' => false,
                'reference_id' => null,
                'error' => 'SMS Gateway exception: ' . $e->getMessage(),
            ];
        }

        // Record message in history
        $messageRecord = $this->recordMessage([
            'online_intake_id' => $onlineIntake->id,
            'intake_id' => null,
            'client_id' => null,
            'beneficiary_name' => $onlineIntake->beneficiary_full_name,
            'recipient_name' => $recipientName,
            'recipient_contact_number' => $normalizedPhone,
            'message_body' => $messageText,
            'message_type' => 'Online Application Rejected',
            'status' => $smsResult['success'] ? 'Sent' : 'Failed',
            'reference_id' => $smsResult['reference_id'] ?? null,
            'error_message' => $smsResult['error'] ?? null,
            'sent_by' => $officerName,
            'sent_at' => $smsResult['success'] ? now() : null,
        ]);

        // Update online intake SMS tracking fields
        if ($smsResult['success']) {
            $onlineIntake->update([
                'sms_status' => 'Sent',
                'sms_error' => null,
                'sms_notified_at' => now(),
                'rejection_sms_sent_at' => now(),
            ]);

            Log::info("[OnlineFinancialSMS] Rejection SMS successfully sent to {$normalizedPhone} for Intake #{$onlineIntake->id}");

            return [
                'success' => true,
                'message_id' => $messageRecord->id,
                'message' => 'Matagumpay na naipadala ang SMS sa aplikante.',
                'phone' => $normalizedPhone,
            ];
        }

        $onlineIntake->update([
            'sms_status' => 'Failed',
            'sms_error' => $smsResult['error'] ?? 'SMS provider delivery error.',
        ]);

        Log::warning("[OnlineFinancialSMS] Rejection SMS failed for Intake #{$onlineIntake->id}: " . ($smsResult['error'] ?? 'Unknown error'));

        return [
            'success' => false,
            'message_id' => $messageRecord->id,
            'error' => $smsResult['error'] ?? 'Nabigo ang pagpapadala ng SMS.',
            'phone' => $normalizedPhone,
        ];
    }

    /**
     * Retry sending SMS notification for a previously failed online intake application.
     */
    public function retryNotification(OnlineFinancialIntake $onlineIntake, ?string $officer = null): array
    {
        if ($onlineIntake->status === 'Accepted') {
            return $this->notifyApproval($onlineIntake, $onlineIntake->beneficiaryIntake, $officer);
        }

        if ($onlineIntake->status === 'Rejected') {
            return $this->notifyRejection($onlineIntake, $officer);
        }

        return [
            'success' => false,
            'message' => 'Hindi maaaring magpadala ng SMS sa aplikasyong nakabinbin pa (For Review).',
        ];
    }

    /**
     * Check if an SMS notification of the specified type was already successfully sent.
     */
    protected function hasAlreadySent(int $onlineIntakeId, string $messageType): bool
    {
        return FinancialAssistanceMessage::where('online_intake_id', $onlineIntakeId)
            ->where('message_type', $messageType)
            ->where('status', 'Sent')
            ->exists();
    }

    /**
     * Persist SMS message record into the database log.
     */
    protected function recordMessage(array $attributes): FinancialAssistanceMessage
    {
        return FinancialAssistanceMessage::create($attributes);
    }
}
