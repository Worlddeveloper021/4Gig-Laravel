<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'plan' => PlanResource::make($this->plan),
            'price' => $this->price,
            'duration' => $this->duration,
            'payment_id' => $this->payment_id,
            'payment_status' => $this->payment_status,
            'status' => $this->status_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
        ];
    }
}
