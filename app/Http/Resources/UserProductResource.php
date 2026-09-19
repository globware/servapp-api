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
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "price" => $this->price,
            "address" => $this->address,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "active" => $this->active,
            "is_unclaimed" => false, // Distinguish from unclaimed leads
            "user" => new UserResource($this->whenLoaded("user")),
            "media" => FileResource::collection($this->whenLoaded("media")),
            "requests" => UserProductRequestResource::collection($this->whenLoaded("requests")),
            "chats" => ChatResource::collection($this->whenLoaded("chats"))
        ];
    }
}
