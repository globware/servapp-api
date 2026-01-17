<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class State extends Model
{
    use HasFactory;

    protected $fillable = ["country_id", "name"];
    
    public function country()
    {
        return $this->belongsTo(COuntry::class);
    }

    public function userServices()
    {
        return $this->hasMany(UserService::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class)->orderBy("name");
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function (State $state) {
            if($state->userServices->count() > 0) {
                foreach($state->userServices as $service) {
                    $service->state_id = null;
                    $service->update();
                }
            }

            if($state->locations->count() > 0) {
                foreach($state->locations as $location) $location->delete();
            }
        });
    }
}
