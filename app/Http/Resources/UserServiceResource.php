<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Collection;

use App\Http\Resources\UserResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\CountryResource;
use App\Http\Resources\FileResource;
use App\Http\Resources\ServiceTagResource;
use App\Http\Resources\StateResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\PatronizerResource;
use App\Http\Resources\ServiceRequestResource;
use App\Http\Resources\ServiceReviewResource;
use App\Http\Resources\ChatResource;

class UserServiceResource extends JsonResource
{
    protected $userRequests;

    public function __construct($resource, $requests = null)
    {
        parent::__construct($resource);
        $this->userRequests = $requests;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resource = [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "phoneNumbers" => $this->phone_numbers,
            "rating" => $this->rating(),
            "verified" => $this->verified,
            "suspended" => $this->suspended,
            "longitude" => $this->longitude,
            "latitude" => $this->latitude,
            "coverPhoto" => new FileResource($this->coverPhoto),
            "user" => new UserResource($this->whenLoaded("user")),
            "service" => new ServiceResource($this->whenLoaded("service")),
            "country" => new CountryResource($this->whenLoaded("country")),
            "state" => new StateResource($this->whenLoaded("state")),
            "location" => new LocationResource($this->whenLoaded("location")),
            "media" => FileResource::collection($this->whenLoaded("media")),
            "tags" => ServiceTagResource::collection($this->whenLoaded("tags")),
            "documents" => FileResource::collection($this->whenLoaded("documents")),
            "requestsCount" => $this->whenCounted("requests"),
            "requests" => ServiceRequestResource::collection($this->whenLoaded("requests")),
            "reviews" => ReviewResource::collection($this->whenLoaded("reviews")),
            "patronizers" => PatronizerResource::collection($this->whenLoaded("patronizers")),
            "chats" => ChatResource::collection($this->whenLoaded("chats"))
        ];
        // dd($this->userRequests);
        if($this->userRequests instanceof Collection) {
            // dd($this->userRequests);
            $resource['userRequests'] = ServiceRequestResource::collection($this->userRequests);
        }

        return $resource;
    }
}
