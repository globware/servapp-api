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
            "user_id" => $this->user_id,
            "user_product_id" => $this->user_product_id,
            "Status" => $this->Status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => new UserResource($this->whenLoaded("user")),
            "product" => new UserProductResource($this->whenLoaded("userProduct")),
            "chats" => ChatResource::collection($this->whenLoaded("chats"))
        ];
    }
}
