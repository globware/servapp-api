<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function target()
    {
        return $this->morphTo();
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
