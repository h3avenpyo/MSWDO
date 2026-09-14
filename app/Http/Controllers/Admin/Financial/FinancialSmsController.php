<?php

namespace App\Http\Controllers\Admin\Financial;

use App\Http\Controllers\Controller;
use App\Models\Financial\FinancialAssistanceMessage;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Sms\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialSmsController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send an SMS message to a beneficiary or representative.
     */
    public function send(Request $request)
    {
        $request->validate([
            'intake_id' => ['required', 'integer', 'exists:beneficiary_intakes,id'],
            'message' => ['required', 'string', 'min:3', 'max:1000'],
            'message_type' => ['nullable', 'string', 'max:50'],
            'claiming_date' => ['nullable', 'date'],
            'recipient_contact_number' => ['nullable', 'string', 'max:50'],
        ]);

        $intake = BeneficiaryIntake::with(['client', 'payrollRecord'])->findOrFail($request->intake_id);

        // Determine recipient name and contact number
        $isRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A';
        $beneficiaryName = $intake->beneficiary_full_name;
        $recipientName = $isRep ? $intake->representative_full_name : $beneficiaryName;

        $rawContactNumber = $request->recipient_contact_number 
            ?: ($isRep ? ($intake->rep_contact_number ?: $intake->beneficiary_contact_number) : $intake->beneficiary_contact_number);

        if (empty($rawContactNumber) || trim($rawContactNumber) === 'N/A' || trim($rawContactNumber) === 'No contact') {
            return response()->json([
                'success' => false,
                'message' => 'No contact number is available for this beneficiary.',
            ], 422);
        }

        $normalizedNumber = SmsService::formatToPhilippineInternational($rawContactNumber);
        if (!$normalizedNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Philippine mobile number format. Please verify the phone number (e.g. 09171234567 or +639171234567).',
            ], 422);
        }

        // Update claiming date if provided in request
        if ($request->filled('claiming_date')) {
            $intake->claiming_date = Carbon::parse($request->claiming_date)->format('Y-m-d');
            $intake->save();
        }

        $effectiveClaimingDate = $intake->effective_claiming_date;
        $messageType = $request->message_type ?: 'Unclaimed Assistance';

        // Warning validation: If type is Unclaimed Assistance, ensure a claiming date exists
        if ($messageType === 'Unclaimed Assistance' && empty($effectiveClaimingDate)) {
            return response()->json([
                'success' => false,
                'message' => 'No claiming date assigned. Please assign a claiming date before sending an unclaimed assistance notification.',
            ], 422);
        }

        $officer = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Staff';

        // Dispatch SMS
        $result = $this->smsService->send($normalizedNumber, $request->message);

        // Record message in history
        $messageRecord = FinancialAssistanceMessage::create([
            'intake_id' => $intake->id,
            'client_id' => $intake->client_id,
            'beneficiary_name' => $beneficiaryName,
            'recipient_name' => $recipientName,
            'recipient_contact_number' => $normalizedNumber,
            'message_body' => trim($request->message),
            'message_type' => $messageType,
            'claiming_date' => $effectiveClaimingDate ? $effectiveClaimingDate->format('Y-m-d') : null,
            'status' => $result['success'] ? 'Sent' : 'Failed',
            'reference_id' => $result['reference_id'] ?? null,
            'error_message' => $result['error'] ?? null,
            'sent_by' => $officer,
            'sent_at' => $result['success'] ? now() : null,
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully.',
                'data' => [
                    'message_id' => $messageRecord->id,
                    'status' => 'Sent',
                    'formatted_sent_at' => $messageRecord->formatted_sent_at,
                    'last_sent_date' => $messageRecord->formatted_date,
                    'reference_id' => $messageRecord->reference_id,
                    'intake_id' => $intake->id,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send message: ' . ($result['error'] ?? 'SMS provider error.'),
            'data' => [
                'message_id' => $messageRecord->id,
                'status' => 'Failed',
                'intake_id' => $intake->id,
            ],
        ], 422);
    }

    /**
     * Get message history for a specific intake.
     */
    public function history($intakeId)
    {
        $intake = BeneficiaryIntake::findOrFail($intakeId);

        $messages = FinancialAssistanceMessage::where('intake_id', $intake->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'date' => $msg->formatted_sent_at,
                    'date_short' => $msg->formatted_date,
                    'message' => $msg->message_body,
                    'type' => $msg->message_type,
                    'status' => $msg->status,
                    'recipient' => $msg->recipient_name . ' (' . $msg->recipient_contact_number . ')',
                    'claiming_date' => $msg->formatted_claiming_date ?? 'N/A',
                    'reference_id' => $msg->reference_id ?? 'N/A',
                    'sent_by' => $msg->sent_by ?? 'MSWDO Staff',
                    'error_message' => $msg->error_message,
                ];
            });

        return response()->json([
            'success' => true,
            'intake_id' => $intake->id,
            'beneficiary_name' => $intake->beneficiary_full_name,
            'messages' => $messages,
            'total' => $messages->count(),
            'last_sent_date' => $intake->last_message_sent_at_formatted,
        ]);
    }

    /**
     * Update claiming date for an intake record.
     */
    public function updateClaimingDate(Request $request, $id)
    {
        $request->validate([
            'claiming_date' => ['required', 'date'],
        ]);

        $intake = BeneficiaryIntake::findOrFail($id);
        $intake->claiming_date = Carbon::parse($request->claiming_date)->format('Y-m-d');
        $intake->save();

        return response()->json([
            'success' => true,
            'message' => 'Claiming date updated successfully.',
            'claiming_date' => $intake->claiming_date->format('Y-m-d'),
            'formatted_claiming_date' => $intake->formatted_claiming_date,
        ]);
    }

    /**
     * Get unclaimed financial assistance beneficiaries for a specified month.
     */
    public function getUnclaimedByMonth(Request $request)
    {
        // Fetch distinct available months where payrolls exist
        $availableMonths = DB::table('financial_payroll_records')
            ->whereNotNull('payroll_date')
            ->selectRaw('DATE_FORMAT(payroll_date, "%Y-%m") as month_key, DATE_FORMAT(payroll_date, "%M %Y") as month_label')
            ->groupBy('month_key', 'month_label')
            ->orderBy('month_key', 'desc')
            ->get();

        if ($availableMonths->isEmpty()) {
            $availableMonths = DB::table('beneficiary_intakes')
                ->whereNotNull('payroll_date')
                ->selectRaw('DATE_FORMAT(payroll_date, "%Y-%m") as month_key, DATE_FORMAT(payroll_date, "%M %Y") as month_label')
                ->groupBy('month_key', 'month_label')
                ->orderBy('month_key', 'desc')
                ->get();
        }

        $requestedMonth = $request->query('month');
        if ($requestedMonth && preg_match('/^\d{4}-\d{2}$/', $requestedMonth)) {
            $selectedMonth = $requestedMonth;
        } elseif ($availableMonths->isNotEmpty()) {
            $selectedMonth = $availableMonths->first()->month_key;
        } else {
            $selectedMonth = Carbon::now()->format('Y-m');
        }

        $parts = explode('-', $selectedMonth);
        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $monthLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');

        // Ensure current selectedMonth is present in availableMonths
        if (!$availableMonths->contains('month_key', $selectedMonth)) {
            $availableMonths->prepend((object) [
                'month_key' => $selectedMonth,
                'month_label' => $monthLabel,
                'unclaimed_count' => 0,
            ]);
        }

        // Attach unclaimed counts per month
        $availableMonths = $availableMonths->map(function ($m) {
            $mParts = explode('-', $m->month_key);
            $mYear = (int) $mParts[0];
            $mMonth = (int) $mParts[1];

            $count = BeneficiaryIntake::where('is_payroll_generated', true)
                ->where(function ($q) {
                    $q->where('claim_status', '!=', 'Claimed')
                      ->orWhereNull('claim_status');
                })
                ->where(function ($q) use ($mYear, $mMonth) {
                    $q->whereYear('payroll_date', $mYear)->whereMonth('payroll_date', $mMonth);
                })
                ->count();

            $m->unclaimed_count = $count;
            return $m;
        });

        // Query unclaimed intakes for this month
        $intakes = BeneficiaryIntake::with(['client', 'payrollRecord', 'latestMessage'])
            ->where('is_payroll_generated', true)
            ->where(function ($q) {
                $q->where('claim_status', '!=', 'Claimed')
                  ->orWhereNull('claim_status');
            })
            ->where(function ($q) use ($year, $month) {
                $q->where(function ($sq) use ($year, $month) {
                    $sq->whereYear('payroll_date', $year)->whereMonth('payroll_date', $month);
                })->orWhere(function ($sq) use ($year, $month) {
                    $sq->whereNull('payroll_date')->whereYear('date_processed', $year)->whereMonth('date_processed', $month);
                });
            })
            ->orderBy('beneficiary_last_name', 'asc')
            ->orderBy('beneficiary_first_name', 'asc')
            ->get();

        $records = $intakes->map(function ($intake, $index) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'N/A';
            $isRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A';
            $repName = $isRep ? $intake->representative_full_name : null;

            $rawContact = $intake->beneficiary_contact_number;
            if ($isRep && !empty($intake->rep_contact_number)) {
                $rawContact = $intake->rep_contact_number;
            }

            $normalizedContact = SmsService::formatToPhilippineInternational($rawContact);
            $hasValidContact = !empty($normalizedContact);

            $claimingDate = $intake->effective_claiming_date;
            $formattedClaimingDate = $claimingDate ? $claimingDate->format('F d, Y') : null;
            $rawClaimingDate = $claimingDate ? $claimingDate->format('Y-m-d') : null;

            return [
                'id' => $intake->id,
                'item_no' => $index + 1,
                'control_number' => $intake->control_number,
                'beneficiary_name' => $beneficiaryName,
                'representative_name' => $repName,
                'is_separate_rep' => $isRep,
                'barangay' => $intake->beneficiary_barangay ?: 'Silang, Cavite',
                'contact_number' => $rawContact ?: 'No contact',
                'normalized_contact' => $normalizedContact,
                'has_valid_contact' => $hasValidContact,
                'amount' => (float) ($intake->recommended_amount ?? 0),
                'formatted_amount' => '₱' . number_format((float) ($intake->recommended_amount ?? 0), 2),
                'claiming_date' => $formattedClaimingDate,
                'raw_claiming_date' => $rawClaimingDate,
                'purpose' => $intake->display_assistance_purpose,
                'last_message_date' => $intake->last_message_sent_at_formatted,
                'has_sent_message' => !empty($intake->last_message_sent_at_formatted),
            ];
        });

        return response()->json([
            'success' => true,
            'month' => $selectedMonth,
            'month_label' => $monthLabel,
            'available_months' => $availableMonths,
            'total_unclaimed' => $records->count(),
            'valid_contact_count' => $records->where('has_valid_contact', true)->count(),
            'missing_contact_count' => $records->where('has_valid_contact', false)->count(),
            'records' => $records,
        ]);
    }

    /**
     * Send bulk SMS messages to unclaimed beneficiaries for a month.
     */
    public function sendBulkUnclaimed(Request $request)
    {
        $request->validate([
            'month' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'intake_ids' => ['nullable', 'array'],
            'intake_ids.*' => ['integer', 'exists:beneficiary_intakes,id'],
            'claiming_date' => ['nullable', 'date'],
            'message' => ['nullable', 'string', 'min:3', 'max:1000'],
        ]);

        $selectedMonth = $request->month;
        $parts = explode('-', $selectedMonth);
        $year = (int) $parts[0];
        $month = (int) $parts[1];
        $monthLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');

        $query = BeneficiaryIntake::with(['client', 'payrollRecord', 'latestMessage'])
            ->where('is_payroll_generated', true)
            ->where(function ($q) {
                $q->where('claim_status', '!=', 'Claimed')
                  ->orWhereNull('claim_status');
            })
            ->where(function ($q) use ($year, $month) {
                $q->where(function ($sq) use ($year, $month) {
                    $sq->whereYear('payroll_date', $year)->whereMonth('payroll_date', $month);
                })->orWhere(function ($sq) use ($year, $month) {
                    $sq->whereNull('payroll_date')->whereYear('date_processed', $year)->whereMonth('date_processed', $month);
                });
            });

        // If specific intake_ids were selected
        if (!empty($request->intake_ids)) {
            $query->whereIn('id', $request->intake_ids);
        }

        $intakes = $query->get();

        if ($intakes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No unclaimed beneficiaries found for {$monthLabel}.",
            ], 422);
        }

        $officer = session('financial_step2_authorized_user') ?? session('admin_user_name') ?? 'MSWDO Staff';

        // Prepare batch claiming date if provided
        $batchClaimingDate = null;
        $batchFormattedClaimingDate = null;
        if ($request->filled('claiming_date')) {
            $parsedDate = Carbon::parse($request->claiming_date);
            $batchClaimingDate = $parsedDate->format('Y-m-d');
            $batchFormattedClaimingDate = $parsedDate->format('F d, Y');
        }

        $templateMessage = $request->filled('message')
            ? trim($request->message)
            : SmsService::generateUnclaimedMessage('{NAME}', '{DATE}');

        $totalAttempted = $intakes->count();
        $sentCount = 0;
        $failedCount = 0;
        $noContactCount = 0;
        $details = [];

        foreach ($intakes as $intake) {
            $beneficiaryName = $intake->beneficiary_full_name ?? 'Beneficiary';
            $isRep = $intake->has_representative && !empty(trim($intake->representative_full_name ?? '')) && $intake->representative_full_name !== 'N/A';
            $recipientName = $isRep ? $intake->representative_full_name : $beneficiaryName;

            $rawContact = $intake->beneficiary_contact_number;
            if ($isRep && !empty($intake->rep_contact_number)) {
                $rawContact = $intake->rep_contact_number;
            }

            $normalizedNumber = SmsService::formatToPhilippineInternational($rawContact);

            // Apply selected batch claiming date to all beneficiaries included in this bulk SMS
            if (!empty($batchClaimingDate)) {
                $intake->claiming_date = $batchClaimingDate;
                $intake->save();
            }

            $effectiveClaimingDate = $intake->effective_claiming_date;
            $formattedClaimingDate = $batchFormattedClaimingDate ?: ($effectiveClaimingDate ? $effectiveClaimingDate->format('F d, Y') : null);

            // Personalize message with beneficiary's name and the claiming date
            $personalizedMessage = str_replace(
                [
                    '[Beneficiary Name]', '[beneficiary name]', '[Pangalan ng Benepisyaryo]', '[Pangalan]', '[pangalan]', '{NAME}', '{name}',
                    '[Claiming Date]', '[claiming date]', '[Petsa ng Pagkuha]', '[Petsa]', '[petsa]', '{DATE}', '{date}'
                ],
                [
                    $beneficiaryName, $beneficiaryName, $beneficiaryName, $beneficiaryName, $beneficiaryName, $beneficiaryName, $beneficiaryName,
                    $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]', $formattedClaimingDate ?: '[Claiming Date]'
                ],
                $templateMessage
            );

            // Missing or invalid contact number check
            if (!$normalizedNumber) {
                $noContactCount++;
                FinancialAssistanceMessage::create([
                    'intake_id' => $intake->id,
                    'client_id' => $intake->client_id,
                    'beneficiary_name' => $beneficiaryName,
                    'recipient_name' => $recipientName,
                    'recipient_contact_number' => $rawContact ?: 'N/A',
                    'message_body' => $personalizedMessage,
                    'message_type' => 'Unclaimed Assistance',
                    'claiming_date' => $effectiveClaimingDate ? $effectiveClaimingDate->format('Y-m-d') : $batchClaimingDate,
                    'status' => 'Failed',
                    'error_message' => 'No contact number or invalid Philippine mobile number format.',
                    'sent_by' => $officer,
                    'sent_at' => null,
                ]);

                $details[] = [
                    'intake_id' => $intake->id,
                    'beneficiary_name' => $beneficiaryName,
                    'status' => 'no_contact',
                    'message' => 'No contact number or invalid Philippine mobile format.',
                ];
                continue;
            }

            // Send via SmsService
            $result = $this->smsService->send($normalizedNumber, $personalizedMessage);

            if ($result['success']) {
                $sentCount++;
                FinancialAssistanceMessage::create([
                    'intake_id' => $intake->id,
                    'client_id' => $intake->client_id,
                    'beneficiary_name' => $beneficiaryName,
                    'recipient_name' => $recipientName,
                    'recipient_contact_number' => $normalizedNumber,
                    'message_body' => $personalizedMessage,
                    'message_type' => 'Unclaimed Assistance',
                    'claiming_date' => $effectiveClaimingDate ? $effectiveClaimingDate->format('Y-m-d') : $batchClaimingDate,
                    'status' => 'Sent',
                    'reference_id' => $result['reference_id'] ?? null,
                    'sent_by' => $officer,
                    'sent_at' => now(),
                ]);

                $details[] = [
                    'intake_id' => $intake->id,
                    'beneficiary_name' => $beneficiaryName,
                    'status' => 'sent',
                    'message' => 'Sent successfully.',
                ];
            } else {
                $failedCount++;
                FinancialAssistanceMessage::create([
                    'intake_id' => $intake->id,
                    'client_id' => $intake->client_id,
                    'beneficiary_name' => $beneficiaryName,
                    'recipient_name' => $recipientName,
                    'recipient_contact_number' => $normalizedNumber,
                    'message_body' => $message,
                    'message_type' => 'Unclaimed Assistance',
                    'claiming_date' => $effectiveClaimingDate ? $effectiveClaimingDate->format('Y-m-d') : null,
                    'status' => 'Failed',
                    'error_message' => $result['error'] ?? 'Provider delivery failure.',
                    'sent_by' => $officer,
                    'sent_at' => null,
                ]);

                $details[] = [
                    'intake_id' => $intake->id,
                    'beneficiary_name' => $beneficiaryName,
                    'status' => 'failed',
                    'message' => $result['error'] ?? 'Delivery failed.',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk messaging completed for {$monthLabel}.",
            'month' => $selectedMonth,
            'month_label' => $monthLabel,
            'total_attempted' => $totalAttempted,
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'no_contact_count' => $noContactCount,
            'details' => $details,
        ]);
    }

    /**
     * Check SMS gateway connectivity and active device status.
     */
    public function gatewayStatus()
    {
        $status = $this->smsService->checkGatewayStatus();

        return response()->json([
            'success' => $status['status'] === 'online' || $status['status'] === 'ready',
            'data' => $status,
        ]);
    }
}
