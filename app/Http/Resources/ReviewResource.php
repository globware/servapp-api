<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\UserResource;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\UserProductResource;

use App\Models\UserService;
use App\Models\UserProduct;

class ReviewResource extends JsonResource
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
            "review" => $this->review,
            "target" => $this->when(!$this->whenLoaded("target"), $this->getTarget()), 
            // "user" => new UserResource($this->whenLoaded("user"))
        ];
    }

    public function getTarget()
    {
        return ($this->target_type == UserService::$type) ? new UserServiceResource($this->target) : new UserProductResource($this->target);
    }
}
