<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServiceRequest extends Model
{
    public $type = "App\Models\UserServiceRequest";
    
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function userService()
    {
        return $this->belongsTo(UserService::class, "user_service_id", "id");
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'user_service_request_id', 'id')->orderBy("created_at", "DESC");
    }

    public function service()
    {
        return $this->hasOneThrough(
            Service::class,         // Final model
            UserService::class,     // Intermediate model
            'id',                   // Foreign key on user_service (local key in service_reviews)
            'id',                   // Foreign key on services (local key in user_service)
            'user_service_id',      // Foreign key on service_reviews
            'service_id'            // Foreign key on user_service
        );
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (ServiceRating $rating) {
            //
        });
    }
}
