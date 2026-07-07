<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\AdminResource;
use App\Http\Resources\TicketMessageResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "title" => $this->title,
            "content" => $this->content,
            "inProgress" => $this->in_progress,
            "resolved" => $this->resolved,
            "user" => new UserResource($this->user),
            "resolvedBy" => new AdminResource($this->resolvedBy),
            "messages" => TicketMessageResource::collection($this->whenLoaded("messages")),
            "createdAt" => $this->created_at
        ];
    }
}
