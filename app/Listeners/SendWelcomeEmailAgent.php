<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\AddAgent;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeStaff;
use App\Mail\WelcomeDoctor;


class SendWelcomeEmailAgent
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
    public function handle(AddAgent $event): void
    {
        $user=$event->user;
        $token = $event->token;
        switch ($user->role) {
            case 'staff':
                Mail::to($user->email)->queue(new \App\Mail\WelcomeStaff($user,$token));
                break;
            default:
                Mail::to($user->email)->queue(new \App\Mail\WelcomeDoctor($user,$token));
                break;
        }
    }
}
