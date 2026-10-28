<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderLocationResource extends JsonResource
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
            "userId" => $this->user_id,
            "title" => $this->title,
            "address" => $this->address,
            "latitude" => $this->latitude !== null ? (float) $this->latitude : null,
            "longitude" => $this->longitude !== null ? (float) $this->longitude : null,
            "countryId" => $this->country_id,
            "stateId" => $this->state_id,
            "locationId" => $this->location_id,
            "isDefault" => (bool) $this->is_default,
            "country" => new CountryResource($this->whenLoaded("country")),
            "state" => new StateResource($this->whenLoaded("state")),
            "location" => new LocationResource($this->whenLoaded("location")),
            "createdAt" => $this->created_at?->toISOString(),
            "updatedAt" => $this->updated_at?->toISOString(),
        ];
    }
}
