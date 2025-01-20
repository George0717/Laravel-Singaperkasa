<?php

namespace App\Mail;

use App\Models\SalesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(SalesOrder $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        return $this->subject('Payment Due Reminder')
            ->view('emails.index')
            ->with(['order' => $this->order]);
    }
}
