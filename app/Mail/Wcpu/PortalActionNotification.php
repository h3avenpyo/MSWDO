<?php

namespace App\Mail\Wcpu;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PortalActionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $action,
        public string $summary,
        public ?string $pdfContent = null,
        public ?string $pdfFilename = null,
    ) {}

    public function build(): self
    {
        $mail = $this->subject('iSERVE notification: ' . $this->action)
            ->view('wcpu.emails.portal-action-notification');

        if ($this->pdfContent !== null && $this->pdfFilename !== null) {
            $mail->attachData($this->pdfContent, $this->pdfFilename, ['mime' => 'application/pdf']);
        }

        return $mail;
    }
}
