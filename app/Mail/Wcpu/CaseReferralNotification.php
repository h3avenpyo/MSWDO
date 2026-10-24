<?php

namespace App\Mail\Wcpu;

use App\Models\Wcpu\CaseReferral;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CaseReferralNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $referral;

    public function __construct(CaseReferral $referral)
    {
        $this->referral = $referral;
    }

    public function build()
    {
        $sender = $this->mailer === 'wcpu' ? 'mail.mailers.wcpu.from' : 'mail.from';
        return $this->from(config($sender . '.address'), config($sender . '.name'))
                    ->subject('New Case Referral - ' . $this->referral->referral_number)
                    ->view('wcpu.emails.case-referral')
                    ->with([
                        'referral' => $this->referral,
                        'case' => $this->referral->case,
                    ]);
    }
}
