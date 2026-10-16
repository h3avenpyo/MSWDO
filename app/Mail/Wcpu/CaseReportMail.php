<?php

namespace App\Mail\Wcpu;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CaseReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $case;
    public $barangayName;
    protected string $rawHtml;
    protected ?string $pdfBytes;

    public function __construct($case, string $rawHtml, string $barangayName, ?string $pdfBytes = null)
    {
        $this->case         = $case;
        $this->rawHtml      = $rawHtml;
        $this->barangayName = $barangayName; // already a resolved name now
        $this->pdfBytes     = $pdfBytes;
    }

    public function build()
    {
        $number  = $this->case->case_number ?: ('ID-'.$this->case->id);
        $status  = $this->case->status ?: 'Unknown';
        $created = optional($this->case->created_at)->format('Y-m-d') ?? now()->format('Y-m-d');
        $subject = "Case Report {$number} - {$status} ({$created})";

        $barangayDisplay = $this->barangayName ?: 'N/A';

        $mail = $this->subject($subject)
            ->text('wcpu.emails.case_report_plain', [
                'number'       => $number,
                'status'       => $status,
                'barangayName' => $barangayDisplay,
                'generated'    => now('Asia/Manila')->format('F j, Y g:i A T'),
                'hasPdf'       => (bool)$this->pdfBytes,
            ]);

        if ($this->pdfBytes) {
            $mail->attachData(
                $this->pdfBytes,
                'case-report-' . ($this->case->case_number ?: $this->case->id) . '.pdf',
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
