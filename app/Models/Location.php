<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;
    
    public function scopeNear($query, $lat, $lng, $radius = 10)
    {
        return $query->selectRaw("
            *, (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance
        ", [$lat, $lng, $lat])
        ->having('distance', '<=', $radius)
        ->orderBy('distance');
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'user_services',  // pivot table name
            'location_id',    // foreign key on pivot table for this model
            'service_id'      // foreign key on pivot table for related model
        );
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (Location $location) {
            if($location->userServices->count() > 0) {
                foreach($location->userServices as $service) {
                    $service->state_id = null;
                    $service->update();
                }
            }
        });
    }
}
