<?php

namespace App\Actions;

use App\DataTransferObjects\PaymentData;
use App\Enum\PaymentStatusEnum;
use App\Models\Payment;

class UpsertPaymentAction
{
    public function execute(Payment $payment, PaymentData $paymentData)
    {
        $payment->client_id = $paymentData->client->id;
        $payment->expires_at = $paymentData->expires_at;
        $payment->status = PaymentStatusEnum::PENDING;
        $payment->save();

        return $payment;
    }
}
