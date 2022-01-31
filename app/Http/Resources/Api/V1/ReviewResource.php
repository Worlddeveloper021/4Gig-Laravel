<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'review' => $this->review,
            'rate' => $this->rate,
            'created_at' => $this->created_at->diffForHumans(),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'profile' => ProfileResource::make($this->whenLoaded('profile')),
        ];
    }
}
