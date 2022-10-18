<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'id' => $this->uuid,
            'email' => $this->email,
            'join_at' => $this->created_at->format('Y-m-d'),
            'links' => [
                'self' => route('clients.show', ['client' => $this->uuid]),
            ],
        ];
    }
}
