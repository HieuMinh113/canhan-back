<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\AppointmentCreated;
use App\Mail\ThankForAppointment;
class AppointmentApply
{
    /**
     * Create the event listener.
     */

    /**
     * Handle the event.
     */
    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;

        if (!empty($appointment->email)) {
            Mail::to($appointment->email)->queue(new ThankForAppointment($appointment));
        }
    }
}
