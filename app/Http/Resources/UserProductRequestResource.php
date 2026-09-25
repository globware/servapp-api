<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProductRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "userId" => $this->user_id,
            "userProductId" => $this->user_product_id,
            "status" => $this->Status,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "user" => new UserResource($this->whenLoaded("user")),
            "product" => new UserProductResource($this->whenLoaded("userProduct")),
            "chats" => ChatResource::collection($this->whenLoaded("chats"))
        ];
    }
}
