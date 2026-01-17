<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\StateResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\ServiceResource;

class LocationResource extends JsonResource
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
            "state" => new StateResource($this->whenLoaded("state")),
            "userServices" => UserServiceResource::collection($this->whenLoaded("userServices")),
            "services" => ServiceResource::collection($this->whenLoaded("services"))
        ];
    }
}
