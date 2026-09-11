<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnclaimedLeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'lead_type' => $this->lead_type,
            'category_text' => $this->category_text,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'approved' => $this->approved,
            'is_unclaimed' => true,
        ];
    }
}
