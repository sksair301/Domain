<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'domain_id' => $this->domain_id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_status_id' => $this->payment_status_id,
            'domain' => new DomainResource($this->whenLoaded('domain')),
            'status' => $this->whenLoaded('status', function () {
                return [
                    'id' => $this->status->id,
                    'name' => $this->status->name,
                ];
            }),
        ];
    }
}
