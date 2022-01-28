<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
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
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nationality' => $this->nationality,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender_name,
            'availability_on_demand' => $this->availability_on_demand,
            'per_hour' => $this->per_hour,
            'avatar' => $this->user->getFirstMediaUrl(User::AVATAR_COLLECTION_NAME),
            'user' => UserResource::make($this->user),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'spoken_languages' => SpokenLanguageResource::collection($this->whenLoaded('spoken_languages')),
            'description' => $this->description ?? '',
            'category' => CategoryResource::make($this->category),
            'sub_category' => CategoryResource::make($this->sub_category),
            'video_presentation' => $this->getFirstMediaUrl(Profile::PRESENTATION_COLLECTION_NAME),
            'portfolio' => $this->getFirstMediaUrl(Profile::PORTFOLIO_COLLECTION_NAME),
            'rate' => rand(1, 5), // TODO: get from DB
            // 'rate' => $this->rate,
        ];
    }
}
