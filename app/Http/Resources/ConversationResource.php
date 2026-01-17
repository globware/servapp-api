<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\MessageResource;
use App\Models\User;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = is_array($this->resource) ? $this->resource : $this->resource->toArray();
        
        $otherParty = $data['otherParty'] ?? null;
        $otherPartyType = $data['otherPartyType'] ?? null;
        
        return [
            'id' => $data['otherPartyId'] ?? null,
            'type' => $otherPartyType === User::$type ? 'user' : 'service',
            'name' => $data['name'] ?? 'Unknown',
            // 'otherParty' => $otherParty ? (
            //     $otherPartyType === User::class 
            //         ? new UserResource($otherParty)
            //         : new UserServiceResource($otherParty)
            // ) : null,
            'latestMessage' => isset($data['latestMessage']) && $data['latestMessage']
                ? new MessageResource($data['latestMessage'])
                : null,
            'unreadCount' => $data['unreadCount'] ?? 0,
            'messages' => MessageResource::collection($data['messages'] ?? []),
            'updatedAt' => isset($data['latestMessage']) && $data['latestMessage']
                ? $data['latestMessage']->created_at 
                : null,
        ];
    }
}

