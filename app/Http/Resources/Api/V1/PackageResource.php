<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'price' => $this->price,
            'duration' => $this->duration,
            'description' => $this->description,
            'on_demand' => $this->on_demand,
            'status' => $this->status_name,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
        ];
    }
}
