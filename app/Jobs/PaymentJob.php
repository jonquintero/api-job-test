<?php

namespace App\Jobs;

use App\Enum\PaymentStatusEnum;
use App\Events\PaymentSuccessUpdated;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private readonly Payment $payment)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $searchLastPaymentToTheClient = Payment::query()
            ->where('client_id', $this->payment->client_id)
            ->where('id', '<>', $this->payment->id)
            ->latest()
            ->first();

        $rate = $this->getClpUsd($searchLastPaymentToTheClient);

        $this->payment->payment_date = date('Y-m-d');
        $this->payment->status = PaymentStatusEnum::PAID;
        $this->payment->clp_usd = $rate;

        $this->payment->save();

    }


    public function getClpUsd($searchLastPaymentToTheClient): mixed
    {
        if (isset($searchLastPaymentToTheClient->payment_date)
            && $searchLastPaymentToTheClient->payment_date == date('Y-m-d')
            && $searchLastPaymentToTheClient->status == PaymentStatusEnum::PAID) {
            $rate = $searchLastPaymentToTheClient->clp_usd;
        } else {
            /*
             * IT CAN PERFECTLY WORK THIS WAY
             * Http::get(config('services.rate.url'))->collect('serie.0')
             * BUT IF I WANT TO SEARCH FOR A SPECIFIC DATE IN THE FUTURE I WILL NOT BE ABLE TO DO IT,
             * THAT'S WHY A SLIGHTLY LONGER PROCEDURE IS USED
             */

            $getRates = Http::get(config('services.rate.url'))->collect('serie');

            $rate = $getRates->where('fecha', '=', date('Y-m-d') . 'T03:00:00.000Z')
                ->flatten(0)
                ->get(1);
        }
        return $rate;
    }
}
