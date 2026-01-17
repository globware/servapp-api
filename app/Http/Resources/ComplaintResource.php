<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserProductResource;
use App\Http\Resources\UserServiceResource;

use App\Models\User;
use App\Models\UserService;
use App\Models\UserProduct;

class ComplaintResource extends JsonResource
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
            "title" => $this->title,
            "content" => $this->content,
            "closed" => $this->closed,
            "user" => new UserResource($this->complainer),
            "target" => $this->getTarget(),
            "reference" => $this->getReference(),
            // "closedBy" => new UserResource($this->closedBy)
        ];
    }

    public function getTarget()
    {
        switch($this->target_type) {
            case User::$type : return new UserResource($this->target); break;
            case UserService::$type : return new UserServiceResource($this->target); break;
            case UserProduct::$type : return new UserProductResource($this->target); break;
            default : return null;
        }
    }

    public function getReference()
    {
        switch($this->reference_type) {
            case UserService::$type : return new UserServiceResource($this->reference); break;
            case UserProduct::$type : return new UserProductResource($this->reference); break;
            default : return null;
        }
    }
}
