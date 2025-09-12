<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankForBooking;
use App\Events\BookingCreated;

class BookingApply implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        $booking = $event->booking;

        if (!empty($booking->email)) {
            Mail::to($booking->email)->queue(new ThankForBooking($booking));
        }
    }
}
