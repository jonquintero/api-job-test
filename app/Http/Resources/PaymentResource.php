<?php

namespace App\Http\Resources;

use App\Enum\PaymentStatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            "uuid" => $this->uuid,
            "expires_at" =>  $this->expires_at,
            "payment_date" => $this->payment_date,
            "status" =>  PaymentStatusEnum::from($this->status)->name,
            "clp_usd" => $this->clp_usd,
            "relationships" => [
                'client' => ClientResource::make($this->whenLoaded('client')),
             ]
        ];
    }
}
