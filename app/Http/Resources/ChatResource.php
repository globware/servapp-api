<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\UserProductResource;
use App\Http\Resources\FileResource;

use App\Models\User;
use App\Models\UserService;
use App\Models\UserProduct;
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
            "entity" => ($this->me()) ? $this->entity($this->sender) : $this->entity($this->receiver),
            "me" => $this->me(),
            "seen" => $this->when(!$this->me(), $this->seen),
            "sentAt" => $this->created_at,
            "media" => FileResource::collection($this->whenLoaded("media")),
        ];
    }

    private function entity($entity)
    {
        if (!$entity) return null;
        if (method_exists($entity, 'getType')) {
            if ($entity->getType() == User::$type) return new UserResource($entity);
            if ($entity->getType() == UserProductResource::class || (defined('App\Models\UserProduct::class') && $entity instanceof \App\Models\UserProduct)) return new UserProductResource($entity);
        }
        return new UserServiceResource($entity);
    }

    private function me()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Sender is a regular User
        if ($this->sender_type === User::$type || $this->sender_type === 'App\Models\User') {
            return $this->sender_id === $user->id;
        }

        // Sender is a UserService (provider) — compare via the sender's user_id or request's userService owner
        if ($this->sender_type === UserService::$type || $this->sender_type === 'App\Models\UserService') {
            if ($this->sender && isset($this->sender->user_id)) {
                return $this->sender->user_id === $user->id;
            }
            if ($this->request && $this->request->userService) {
                return $this->request->userService->user_id === $user->id;
            }
            if ($this->requestable && isset($this->requestable->userService)) {
                return $this->requestable->userService->user_id === $user->id;
            }
        }

        return false;
    }
}
