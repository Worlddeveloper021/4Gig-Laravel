<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Profile;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender_name,
            'nationality' => $this->nationality,
            'profile_type' => $this->type_name,
            'availability_on_demand' => $this->availability_on_demand,
            'per_hour' => $this->per_hour,
            'avatar' => $this->when($this->relationLoaded('media'), $this->getFirstMediaUrl(Profile::COLLECTION_NAME)),
            'user' => $this->whenLoaded('user'),
        ];
    }
}
