<?php

namespace App\DataTransferObjects;

use App\Http\Requests\UpsertPaymentRequest;
use App\Models\Client;

class PaymentData
{
    public function __construct(
        public readonly string $expires_at,
        public readonly Client $client,

    ) {
    }

    public static function fromRequest(UpsertPaymentRequest $request): self
    {
        return new static(
            $request->expires_at,
            $request->getClient(),
        );
    }
}
