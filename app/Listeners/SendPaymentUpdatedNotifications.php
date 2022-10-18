<?php

namespace App\Listeners;

use App\Events\PaymentUpdated;
use App\Models\Client;
use App\Models\Payment;
use App\Notifications\NewPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentUpdatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PaymentUpdated  $event
     * @return void
     */
    public function handle(PaymentUpdated $event)
    {
        $client = Client::where('id', $event->payment->client_id)->first();
        $client->notify(new NewPayment($event->payment));

    }
}
