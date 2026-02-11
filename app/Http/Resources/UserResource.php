<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\FileResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\MessageResource;

class UserResource extends JsonResource
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
            "firstname" => $this->firstname,
            "surname" => $this->surname,
            "email" => $this->email,
            "phoneNumber" => $this->phone_number,
            "emailVerified" => ($this->email_verified_at) ? true : false,
            "emailVerifiedAt" => $this->email_verified_at,
            "photo" => new FileResource($this->photo),
            "tokens" => $this->tokens,
            "identification" => $this->identification?->name,
            "identificationNo" => $this->identification_no,
            "hasService" => $this->has_service,
            "suspended" => $this->suspended,
            "lastLogin" => $this->last_login,
            "referralCode" => $this->referral_code,
            "referredBy" => new UserResource($this->referredBy),
            "servicesCount" => $this->whenCounted("userServices"),
            "services" => UserServiceResource::collection($this->whenLoaded("userServices")),
            "notifications" => NotificationResource::collection($this->whenLoaded("notifications")),
            "messagesCount" => $this->whenCounted("inbox")
        ];
    }
}
