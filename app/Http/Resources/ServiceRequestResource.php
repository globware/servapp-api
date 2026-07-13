<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\ChatResource;

use App\Services\ChatService;

use App\Models\User;

class ServiceRequestResource extends JsonResource
{

    protected $unSeenChats = null;

    public function __construct($resource, $entityType = null)
    {
        parent::__construct($resource);
        if ($entityType) {
            if ($this->chats->count() == 0) {
                $this->unSeenChats = 0;
            } else {
                $chatService = new ChatService;
                $chatService->seen = false;
                $chatService->receiverId = ($entityType == User::$type) ? $this->user_id : $this->user_service_id;
                $chatService->receiverType = $entityType;
                $chats = $chatService->getMessages($this->id);
                $this->unSeenChats = $chats->count();
            }
        }
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
            "status" => $this->status,
            "message" => $this->message,
            "seen" => $this->seen,
            "completedBy" => $this->completed_by,
            "user" => new UserResource($this->whenLoaded('user')),
            "service" => new UserServiceResource($this->whenLoaded("userService")),
            "chats" => ChatResource::collection($this->chats),
            "hasComplained" => $this->complaints()->where("user_id", Auth::user()->id)->exists(),
            "requestedAt" => $this->created_at
        ];

        if ($this->unSeenChats) $resource['unSeenChats'] = $this->unSeenChats;

        return $resource;
    }
}
