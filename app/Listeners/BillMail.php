<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\BillCreated;
use App\Mail\ThankForBill;

class BillMail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BillCreated $event): void
    {
        $bill = $event->bill;
        
        if (!empty($bill->customer_email)) {
            Mail::to($bill->customer_email)->queue(new ThankForBill($bill));
        }
    }
}
