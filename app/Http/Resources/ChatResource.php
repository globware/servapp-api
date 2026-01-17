<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\UserProductResource;

use App\Models\User;
use App\Models\UserService;
use App\Models\UserServiceRequest;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "message" => $this->message,
            // "entity" => ($this->me()) ? $this->entity($this->sender) : $this->entity($this->receiver),
            "me" => $this->me(),
            "seen" => $this->when(!$this->me(), $this->seen),
            "sentAt" => $this->created_at
        ];
    }

    private function entity($entity)
    {
        if($entity->getType() == User::$type) return new UserResource($entity);
        return new UserServiceResource($entity);
    }

    private function me()
    {
        if($this->sender_type == User::$type && $this->sender_id == Auth::user()->id) return true;
        if($this->request->userService->user_id == Auth::user()->id) return true;
        return false;
    }
}
