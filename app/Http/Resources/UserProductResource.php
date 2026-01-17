<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ServicePatronizerResource;
use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ServiceReviewResource;
use App\Http\Resources\ChatResource;

class UserProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "rating" => $this->rating,
            "verified" => $this->verified,
            "suspended" => $this->suspended,
            "user" => new UserResource($this->whenLoaded("user")),
            "service" => new ServiceResource($this->whenLoaded("service")),
            "country" => new CountryResource($this->whenLoaded("country")),
            "state" => new StateResource($this->whenLoaded("state")),
            "location" => new LocationResource($this->whenLoaded("location")),
            "media" => FileResource::collection($this->whenLoaded("media")),
            "documents" => FileResource::collection($this->whenLoaded("documents")),
            "requests" => ServiceRequestResource::collection($this->whenLoaded("requests")),
            "reviews" => ServiceReviewResource::collection($this->whenLoaded("reviews")),
            "patronizers" => ServicePatronizerResource::collection($this->whenLoaded("patronizers")),
            "chats" => ChatResource::collection($this->whenLoaded("chats"))
        ];
    }
}
