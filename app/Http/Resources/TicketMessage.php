<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\TicketResource;
use App\Http\Resources\AdminResource;

class TicketMessage extends JsonResource
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
            "message" => $this->message,
            "ticket" => new TicketResource($this->whenLoaded("ticket")),
            "admin" => new AdminResource($this->whenLoaded("admin"))
        ];
    }
}
