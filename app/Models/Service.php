<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Helpers;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = ["name"];
    
    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function serviceReviews()
    {
        return $this->hasManyThrough(ServiceReview::class, UserService::class);
    }

    public function serviceRatings()
    {
        return $this->hasManyThrough(ServiceRating::class, UserService::class);
    }

    public function servicePatronizers()
    {
        return $this->hasManyThrough(ServicePatronizer::class, UserService::class);
    }

    public function serviceRequests()
    {
        return $this->hasManyThrough(ServiceRequest::class, UserService::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_service')
                    ->withTimestamps();
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Service $service) {
            // if($service->userServices->count())
        });
    }
}
