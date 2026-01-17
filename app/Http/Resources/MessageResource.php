<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Models\User;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sender = $this->sender;
        $receiver = $this->receiver;
        
        return [
            "id" => $this->id,
            "message" => $this->message,
            "sender" => $sender ? (
                $this->sender_type === User::class 
                    ? new UserResource($sender)
                    : new UserServiceResource($sender)
            ) : null,
            "receiver" => $receiver ? (
                $this->receiver_type === User::class 
                    ? new UserResource($receiver)
                    : new UserServiceResource($receiver)
            ) : null,
            "read" => $this->read,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
        ];
    }
}
