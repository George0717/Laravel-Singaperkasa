<?php

namespace App\Jobs;

use App\Models\SalesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDueDateNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $today = Carbon::today();

        // Ambil semua Sales Orders dengan due_date hari ini
        $salesOrders = SalesOrder::whereDate('due_date', $today)->get();

        foreach ($salesOrders as $order) {
            Mail::to($order->customer_email)->send(new \App\Mail\DueDateReminder($order));
        }
    }
}
