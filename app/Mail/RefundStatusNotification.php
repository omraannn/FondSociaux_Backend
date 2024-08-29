<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RefundStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $refund;
    public $user;
    public $typeFee;

    public function __construct($refund)
    {
        $this->refund = $refund;
        $this->user = $refund->user;
        $this->typeFee = $refund->typeFee;
    }

    public function build()
    {
        return $this->subject('Notification sur le statut de votre demande de remboursement')
            ->view('emails.refund_status_notification');
    }
}
