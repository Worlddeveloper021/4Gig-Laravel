<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'id' => $this->id,
            'customer' => new CustomerResource($this->customer),
            'profile' => new ProfileResource($this->profile),
            'package' => new PackageResource($this->package),
            'payment_id' => $this->payment_id,
            'payment_status' => $this->payment_status,
            'duration' => $this->duration,
            'price' => $this->price,
            'status' => $this->status_name,
            'channel_name' => $this->channel_name,
        ];
    }
}
