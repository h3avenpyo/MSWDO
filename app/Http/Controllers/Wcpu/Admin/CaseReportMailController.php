<?php

namespace App\Http\Controllers\Wcpu\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\Wcpu\CaseReportMail;
use App\Models\Wcpu\CaseModel;
use App\Models\Wcpu\ReportRecipient;
use App\Models\Wcpu\Barangay;

class CaseReportMailController extends Controller
{
    private array $recipients = [];

    public function __construct()
    {
        $useDb = filter_var(env('USE_DB_RECIPIENTS', false), FILTER_VALIDATE_BOOL);

        if ($useDb) {
            $this->recipients = ReportRecipient::active()
                ->orderBy('id')
                ->pluck('email')
                ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                ->values()
                ->all();
        }

        if (empty($this->recipients)) {
            $fallbackList = env('CASE_REPORT_RECIPIENTS'); // optional CSV
            if ($fallbackList) {
                $this->recipients = collect(explode(',', $fallbackList))
                    ->map(fn($e) => trim($e))
                    ->filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
                    ->values()
                    ->all();
            }
        }

        if (empty($this->recipients)) {
            $single = env('CASE_REPORT_FALLBACK');
            if (filter_var($single, FILTER_VALIDATE_EMAIL)) {
                $this->recipients = [$single];
            }
        }
    }

    public function send(Request $request, $id)
    {
        // Collect chosen recipients (optional)
        $chosen = $request->input('recipients', []);
        if (!is_array($chosen)) $chosen = [];
        // Sanitize + validate emails
        $chosen = array_values(array_filter(array_map(function($e){
            $e = trim($e);
            return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : null;
        }, $chosen)));

        // If no chosen provided, fallback to preconfigured list
        $effectiveRecipients = !empty($chosen) ? $chosen : $this->recipients;

        // Must have at least one
        if (empty($effectiveRecipients)) {
            return back()->with('mail_error', 'Select at least one recipient.')->withInput();
        }

        $case = CaseModel::with(['assignedTo','createdBy','barangay'])->findOrFail($id);

        // Resolve barangay name (same logic as before)
        $barangayName = 'N/A';
        if (isset($case->barangay) && $case->barangay && isset($case->barangay->name)) {
            $barangayName = $case->barangay->name;
        } elseif (!empty($case->case_designation)) {
            $raw = trim((string)$case->case_designation);
            if (ctype_digit($raw)) {
                $b = Barangay::select('id','name')->find((int)$raw);
                if ($b && $b->name !== '') $barangayName = $b->name;
            } else {
                $barangayName = $raw;
            }
        }

        // Render the same layout used by print (desk officer/admin)
        $rawHtml = view('desk-officer.case-print', [
            'case' => $case
        ])->render();

        $rawHtml = preg_replace('/<div[^>]*class="[^"]*action-buttons[^"]*"[^>]*>.*?<\/div>/is', '', $rawHtml);

        $pdfBytes = null;
        try {
            // Generate PDF directly from the Blade view for consistent layout/format
            $pdf = Pdf::loadView('desk-officer.case-print', ['case' => $case])->setPaper('a4', 'portrait');
            $pdfBytes = $pdf->output();
        } catch (\Throwable $e) {
            Log::error('PDF generation failed', ['case_id' => $case->id, 'error' => $e->getMessage()]);
        }

        $mailer      = config('mail.default');
        $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS');
        $fromName    = config('mail.from.name') ?: env('MAIL_FROM_NAME', 'Case Report System');

        if (!$fromAddress) {
            Log::warning('Mail send aborted: missing from address.');
            return back()->with('mail_error', 'Email not sent: MAIL_FROM_ADDRESS not configured.');
        }

        $primary = $effectiveRecipients[0];
        $bcc = array_slice($effectiveRecipients, 1);

        try {
            $builder = Mail::mailer($mailer)->to($primary);
            if ($bcc) $builder->bcc($bcc);

            $builder->send(
                (new CaseReportMail($case, $rawHtml, $barangayName, $pdfBytes))
                    ->from($fromAddress, $fromName)
            );

            if (in_array($mailer, ['log','array'])) {
                return back()->with('status', "Simulated email stored via '{$mailer}' driver.")->withInput();
            }

            return back()->with('status', 'Case report email sent to: '.implode(', ', $effectiveRecipients));
        } catch (\Throwable $e) {
            Log::error('Case report mail failed', [
                'case_id'    => $case->id,
                'recipients' => $effectiveRecipients,
                'exception'  => $e->getMessage(),
            ]);
            return back()->with('mail_error', 'Failed to send email.')->withInput();
        }
    
    }
}
