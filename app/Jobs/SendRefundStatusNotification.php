<?php

namespace App\Jobs;

use App\Mail\RefundStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRefundStatusNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $refund;
    /**
     * Create a new job instance.
     */
    public function __construct($refund)
    {
        $this->refund = $refund;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->refund->user->email)->send(new RefundStatusNotification($this->refund));
    }
}
